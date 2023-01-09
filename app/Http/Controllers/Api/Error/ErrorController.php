<?php

namespace App\Http\Controllers\Api\Error;

use App\Mail\ErrorMail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;

class ErrorController extends Controller
{
    public function sendError(Request $request)
    {
        $message = $request->message;

        $mailData = [
            'message' => $message,
        ];

        Mail::to('manuelortegagaliano@gmail.com')->send(new ErrorMail($mailData));
    }
}
