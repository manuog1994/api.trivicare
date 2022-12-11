<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Mail\SubscribeMail;
use Illuminate\Http\Request;
use App\Mail\UnsubscribeMail;
use App\Models\Newsletter;
use Illuminate\Support\Facades\Mail;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if($user) {
            $user->newsletter = true;
            $user->save();

            $mailData = [
                'email' => $user->email,
                'name' => $user->name,
            ];

            Mail::to($user->email)->send(new SubscribeMail($mailData));
            
            return response()->json(['message' => 'You are already subscribed to our newsletter.']);
        } else {
            $newsletter = Newsletter::create([
                'email' => $request->email,
            ]);

            $mailData = [
                'email' => $newsletter->email,
            ];

            Mail::to($newsletter->email)->send(new SubscribeMail($mailData));
            
            return response()->json(['message' => 'You are already subscribed to our newsletter.']); 
        }
    }

    public function unsubscribe(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        $newsletter = Newsletter::where('email', $request->email)->first();


        if($user && $newsletter) {
            $user->newsletter = false;
            $user->save();
            $newsletter->delete();

            $mailData = [
                'email' => $user->email,
                'name' => $user->name,
            ];

            Mail::to($user->email)->send(new UnsubscribeMail($mailData));
            
            return response()->json(['message' => 'You are already unsubscribed from our newsletter.']);
            
        } else if($user) {

            $user->newsletter = false;
            $user->save();

            $mailData = [
                'email' => $user->email,
                'name' => $user->name,
            ];

            Mail::to($user->email)->send(new UnsubscribeMail($mailData));
            
            return response()->json(['message' => 'You are already unsubscribed from our newsletter.']); 

        } else {
            
            $mailData = [
                'email' => $newsletter->email,
            ];

            Mail::to($newsletter->email)->send(new UnsubscribeMail($mailData));
            
            $newsletter->delete();

            return response()->json(['message' => 'You are already unsubscribed from our newsletter.']);
        }
    }
}
