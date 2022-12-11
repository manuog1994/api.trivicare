<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FirstOrderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $dataOne;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($dataOne)
    {
        $this->dataOne = $dataOne;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('TriviCare | Gracias por tu primera compra')->view('emails.first-order');
    }
}
