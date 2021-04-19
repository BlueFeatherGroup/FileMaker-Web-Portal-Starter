<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class PaymentAlert extends Mailable
{
    use Queueable, SerializesModels;


    public $paymentAmount;
    public $clientName;
    public $transactionId;
    public $date;
    public $error;
    public $email;


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($paymentAmount, $transactionId, $clientName, $email, $error = null)
    {
        $this->paymentAmount = $paymentAmount;
        $this->transactionId = $transactionId;
        $this->clientName = $clientName;
        $this->email = $email;
        $this->date = Carbon::now()->format('n/j/y');
        $this->error = $error;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // set alert to error if an error was passed in
        $subject = $this->error ? 'Client Portal Payment Error' : 'Client Portal Payment Received';
        return $this->subject($subject)->markdown('emails.payment-alert');
    }
}
