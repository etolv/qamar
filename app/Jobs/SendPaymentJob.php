<?php

namespace App\Jobs;

use App\Mail\PaymentMail;
use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendPaymentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(protected Payment $payment) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $user =  $this->payment->model?->customer?->user;
        if ($user && $email = $user->email) {
            Mail::to($email)->send(new PaymentMail($this->payment));
        }
        if ($user && $phone = $user->phone) {
            //
        }
    }
}
