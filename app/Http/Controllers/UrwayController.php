<?php

namespace App\Http\Controllers;

use App\Helpers\Helpers;
use App\Http\Controllers\front_pages\Payment;
use App\Http\Requests\IndexUrwayRequest;
use App\Services\PaymentService;
use App\Traits\PaymentTrait;
use Illuminate\Http\Request;
use stdClass;

class UrwayController extends Controller
{
    use PaymentTrait;

    public function __construct(private PaymentService $paymentService) {}

    public function index(IndexUrwayRequest $request)
    {
        $data = $request->afterValidation();
        $payment = $this->paymentService->update($data, $data['payment_id']);
        return view('front.payment-page', compact('payment'));
    }

    public function test()
    {
        //
    }
}
