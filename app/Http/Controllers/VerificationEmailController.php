<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Mail\VerificationMail;
use App\Mail\ForgotPasswordMail;
use App\Models\VerificationToken;
use Illuminate\Support\Facades\Mail;

class VerificationEmailController extends Controller
{
    public function verify($token)
    {
        $verification_token = VerificationToken::where('token', $token)->first();
        
        if($verification_token) {
            $verification_token->user->email_verified_at = now();
            $verification_token->user->save();
            $verification_token->delete();
            return view('verification-email');
        } else {
            return redirect()->route('errors.404');
        }
    }

    public function resendEmail($id)
    {
        $user = User::where('id', $id)->first();

        //generate random string
        $rand_token = openssl_random_pseudo_bytes(16);
        //change binary to hexadecimal
        $token = bin2hex($rand_token);

        VerificationToken::create([
            'user_id' => $user->id,
            'token' => $token,
        ]);

        //generate email verification
        $mailData = [
            'title' => 'Aqui tienes tu enlace de verificación de correo electrónico',
            'body' => 'Este es un enlace de verificación de correo electrónico.',
            'email' => $user->email,
            'url' => 'https://api.trivicare.com/verify-email/' . $token,
        ];

        Mail::to($user->email)->send(new VerificationMail($mailData));

        return response()->json([
            'success' => true,
            'message' => 'Email verification link sent to your email.',
        ], 200);
    }


}
