<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Mail\ForgotPasswordMail;
use App\Models\VerificationToken;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordController extends Controller
{
    public function forgotPassword(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if($user) {
            //generate random string
            $rand_token = openssl_random_pseudo_bytes(10);
            //change binary to hexadecimal
            $password = bin2hex($rand_token);

            //update password
            $user->password = bcrypt($password);
            $user->save();

            //generate email verification
            $mailData = [
                'title' => 'Aqui tiene su nueva contraseña.',
                'body' => 'Hemos generado una contraseña temporal para que acceda a su perfil.',
                'email' => $user->email,
                'password' => $password,
            ];

            Mail::to($user->email)->send(new ForgotPasswordMail($mailData));

            return response()->json([
                'success' => true,
                'message' => 'Password reset link sent to your email.',
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }
    }

}
