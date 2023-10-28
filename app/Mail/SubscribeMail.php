<?php

namespace App\Mail;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SubscribeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $mailData;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mailData)
    {
        $this->mailData = $mailData;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // Si la fecha actual es menor que la fecha de expiración, se envia el mail subscribe
        if (Carbon::now()->lessThan(Carbon::parse('2023-11-01'))) {
            return $this->subject('Trivicare.com | Bienvenid@ a nuestro newsletter')->view('emails.subscribe');
        } else {
            return $this->subject('Trivicare.com | Bienvenid@ a nuestro newsletter')->view('emails.subscribe_standard');
        }

    }
}
