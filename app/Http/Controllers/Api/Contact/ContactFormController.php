<?php

namespace App\Http\Controllers\Api\Contact;

use App\Mail\ContactMail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;

class ContactFormController extends Controller
{
    public function contactPost(Request $request){
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'message' => 'required',
            'subject' => 'required',
        ]);

        $mailData = [
            'name' => $request->name,
            'email' => $request->email,
            'message' => $request->message,
            'subject' => $request->subject,
        ];

        Mail::to('cristina@trivicare.com')->send(new ContactMail($mailData));

        return response()->json([
                'success' => true,
                'message' => 'success'
        ], 200);
    }
}
