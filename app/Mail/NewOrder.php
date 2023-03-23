<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewOrder extends Mailable
{
    use Queueable, SerializesModels;

    public $orderToMail;
    /**
     * Create a new message instance.
     *
     * @return void
     */

    public function __construct($orderToMail)
    {
        $this->orderToMail = $orderToMail;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Trivicare.com | Hemos recibido el pago de su pedido')->view('emails.newOrder');
    }
}
