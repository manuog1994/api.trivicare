<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        // Validamos los campos del formulario del cliente
        $request->validate([
            'username' => 'required|email',
            'password' => 'required',
        ]);
        /** @var \App\Models\User $user **/
        $user = User::where('email', $request->username)->first();

        if($user->accessToken()){
            $user->accessToken()->delete();
        }

        
        // Solicitud de token a la API desde cliente externo
        $response = Http::asForm()->post('http://api.trivicare.test/oauth/token', [
            'grant_type' => 'password',
            'client_id' => '973327d9-0c4e-4865-9372-b68d50978ed0',
            'client_secret' => 'VTE2oQd0Wwh0yamf8InqwUs6NTyJm63A5SvMx788',
            'username' => $request->username,
            'password' => $request->password,
            'scope' => '*',
        ]);
           
        $access_token = $response->json();

        $user->accessToken()->create([
            'access_token' => $access_token['access_token'],
            'refresh_token' => $access_token['refresh_token'],
            'expires_at' => now()->addSeconds($access_token['expires_in']),
        ]);
        
        Auth::login($user);


        return response()->json($access_token);
    }

    public function logout(Request $request)
    {
        $request->user()->accessToken()->delete();
        return response()->json(['message' => 'Successfully logged out']);
    }

}
