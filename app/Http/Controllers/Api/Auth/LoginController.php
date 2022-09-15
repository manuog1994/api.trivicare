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
            'client_id' => '973bd2ef-601d-435b-a5b2-666435d949e4',
            'client_secret' => '00jpvr483TqPio35jg1u5GybsDzgKK7k13aHnmUc',
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


        return response()->json([
            $user,
            $access_token,

        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->accessToken()->delete();
        return response()->json(['message' => 'Successfully logged out']);
    }

}
