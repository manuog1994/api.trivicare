<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Newsletter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\AuthenticationException;

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
        $user->save();

        if ($request->newsletter == true) {
            $newsletter = new Newsletter;
            $newsletter->email = $request->email;
            $newsletter->save();
        }
                
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

    //function for refresh token
    public function refresh(Request $request)
    {
        $request->session()->regenerateToken();
        return response()->json(['message' => 'Token refreshed']);
    }


}
