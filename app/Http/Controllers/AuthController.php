<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Mail\VerificationMail;
use App\Models\VerificationToken;
use Illuminate\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Passwords\PasswordBroker;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'string',
            'provider_id' => 'string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error.',
                'errors' => $validator->errors(),
            ], 422);
        }


        if($request->provider_id != null){
            $user = User::where('provider_id', $request->provider_id)->first();

            Auth::login($user);

            return response()->json([
                'user' => $user,
                'provider_id' => $request->provider_id,
            ]);
        }
        
        if(!Auth::attempt($request->only('email', 'password'))){
            throw new AuthenticationException();
        }

        $request->session()->regenerate();

    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|confirmed',
        ]);

        if($validator->fails()){
            return response()->json([
                'success' => false,
                'message' => 'Validation Error.',
                'errors' => $validator->errors(),
            ], 422);
        }
        
        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->newsletter = $request->newsletter;

        $user->save();


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
            'title' => 'Muchas gracias por registrarte en Trivicare.com',
            'body' => 'Gracias por registrarte en Trivicare.com. Ahora puedes disfrutar de todos nuestros servicios.',
            'email' => $user->email,
            'url' => 'https://api.trivicare.com/verify-email/' . $token,
        ];

        Mail::to($user->email)->send(new VerificationMail($mailData));
        
        return response()->json([
            'success' => true,
            'message' => 'User registered successfully.'
        ], 200);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }

}
