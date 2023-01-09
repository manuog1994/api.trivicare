<?php

namespace App\Http\Controllers\Api\Suggestion;

use Illuminate\Http\Request;
use App\Mail\SuggestionMailbox;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;

class SuggestionController extends Controller
{
    public function sendSuggestion(Request $request)
    {
        $mailData = [
            'name' => $request->name,
            'email' => $request->email,
            'type' => $request->type,
            'message' => $request->message,
        ];

        Mail::to('info@trivicare.com')->send(new SuggestionMailbox($mailData));
    }
}
