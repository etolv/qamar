<?php

namespace App\Traits;

use App\Jobs\SendPaymentJob;
use App\Models\Payment;
use Exception;
use Illuminate\Support\Facades\Http;

trait PaymentTrait
{

    public function checkHashedResponse($request)
    {
        $merchant_key = config('services.urway.merchant_key');
        $requestHash = hash('sha256', "$request->TranId|$merchant_key|$request->ResponseCode|$request->amount");
        return $requestHash == $request->responseHash;
    }

    public function hashedTxnDetails($track_id, $amount)
    {
        $terminal_id = config('services.urway.terminal_id');
        $terminal_password = config('services.urway.terminal_password');
        $merchant_key = config('services.urway.merchant_key');
        $txn_details = "$track_id|$terminal_id|$terminal_password|$merchant_key|$amount|SAR";
        return hash('sha256', $txn_details);
    }
    public function createPayment(Payment $payment)
    {
        try {
            $track_id = (string)$payment->id;
            // $track_id = (string)time();
            // $track_id = 1;
            $url = "https://payments-dev.urway-tech.com/URWAYPGService/transaction/jsonProcess/JSONrequest";
            $data = [
                'trackid' => (string)$track_id,
                'terminalId' => config('services.urway.terminal_id'),
                'action' => '1', // purchase
                'customerEmail' => $payment->model?->customer?->user?->email,
                'merchantIp' => '144.76.44.146',
                'password' => config('services.urway.terminal_password'),
                'currency' => 'SAR',
                'amount' => $payment->amount,
                'country' => 'SA',
                // 'metaData' => "{\"entryone\":\"A\",\"entrytwo\":\"J\",\"entrythree\":\"xyz\"}", // nullable
                'requestHash' => $this->hashedTxnDetails($track_id, $payment->amount),
                'udf2' => route('urway.index'),
                'udf3' => 'AR'
            ];
            $response = Http::post($url, $data);
            logger("response body", ['response' => $response->body()]);
            if ($response->successful()) {
                $response = $response->object();
                if ($response->payid && $response->targetUrl) {
                    $payment->update([
                        'data' => json_encode([
                            'payid' => $response->payid,
                            'targetUrl' => $response->targetUrl,
                            'payment_url' => $response->targetUrl . "?paymentid=$response->payid"
                        ])
                    ]);
                    SendPaymentJob::dispatch($payment);
                }
            }
            return $payment;
        } catch (Exception $e) {
            logger("urway payment error", ['error' => $e->getMessage()]);
            return $payment;
        }
    }

    //  request hash: $requestHash="".$_GET['TranId']."|".$merchantKey."|".$_GET['ResponseCode']."|".$_GET['amount']."";
    // https://merchantwebsite.response_page_url?PaymentId=2301614576680095257&TranId=23016
    // 14576680095257&ECI=02&Result=Successful&TrackId=Post_101&AuthCode=175394&Respon
    // seCode=000&RRN=301611175394&responseHash=b3ae0d67a947d515436fb2b2d4405578ba4
    // b19b45427d344ae4acb97a91beae6&cardBrand=MASTER&amount=1.00&UserField1=&UserFiel
    // d3=en&UserField4=&UserField5=&cardToken=&maskedPAN=512345XXXXXX0008&email=&pa
    // yFor=&SubscriptionId=null&PaymentType=CreditCard&metaData=eyJlbnRyeW9uZSI6IkEiLCJlb
    // nRyeXR3byI6IkoiLCJlbnRyeXRocmVlIjoieHl6In0=

    public function transactionQuery($payment)
    {
        try {
            $url = "https://payments-dev.urway-tech.com/URWAYPGService/transaction/jsonProcess/JSONrequest";
            $data = [
                'transid' => $payment->transaction_id,
                'trackid' => $payment->id,
                'terminalId' => config('services.urway.terminal_id'),
                'action' => '10', // inquery
                'merchantIp' => '144.76.44.146',
                'password' => config('services.urway.terminal_password'),
                'currency' => 'SAR',
                'amount' => $payment->amount,
                'country' => 'SA',
                'requestHash' => $this->hashedTxnDetails($payment->id, $$payment->amount),
                'udf1' => '1'
            ];
            $response = Http::get($url, $data);
            if ($response->successful()) {
                return $response->object();
            }
            return false;
        } catch (Exception $e) {
            logger("urway payment error", ['error' => $e->getMessage()]);
            return false;
        }
    }
    // Transaction result (Successful/Failure)
    // {"result":"Successful","responseCode":"000","authcode":"315659","tranid":"2023717620519795287","tracki
    //     d":"6781595593478","terminalid":"celeverAut","udf1":"","udf2":"2020612350872734303","udf3":"","udf4":"",
    //     "udf5":"","rrn":"020612315659","eci":"02","subscriptionId":null,"trandate":"2020-07-24
    //     12:25:16:000550","tranType":"3D","integrationModule":null,"integrationData":null,"payid":null,"targetUrl":nu
    //     ll,"postData":null,"intUrl":null,"responseHash":"caf195b888d2859c6981ae4d01ec613a1892508c91812c68
    //     448a3763a03a98cd","amount":"500.00","cardBrand":"MASTER","maskedPAN":"512345XXXXXX0008","li
    //     nkBasedUrl":null,"sadadNumber":null,"billNumber":null,"paymentType":"CreditCard","metaData":"{\"entryo
    //     ne\":\"A\",\"entrytwo\":\"J\",\"entrythree\":\"xyz\"}","cardToken":""}
}
