<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Receipt extends Mailable
{
    use Queueable, SerializesModels;

    public $paymentAmount;
    public $paymentMethod;
    public $transactionId;
    public $date;


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($paymentAmount, $paymentMethod, $transactionId, $date)
    {
        $this->paymentAmount = $paymentAmount;
        $this->paymentMethod = $paymentMethod;
        $this->transactionId = $transactionId;
        $this->date = $date;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.receipt');
    }
}
