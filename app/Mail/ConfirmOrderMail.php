<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ConfirmOrderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $mailConfirm;
    /**
     * Create a new message instance.
     *
     * @return void
     */

    public function __construct($mailConfirm)
    {
        $this->mailConfirm = $mailConfirm;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Trivicare.com | Confirmación de su pedido')->view('emails.confirmOrder');
    }
}
