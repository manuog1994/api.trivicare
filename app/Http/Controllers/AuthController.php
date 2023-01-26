<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Newsletter;
use Illuminate\Http\Request;
use App\Mail\VerificationMail;
use App\Models\VerificationToken;
use Google\Service\ArtifactRegistry\Hash;
use Illuminate\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Passwords\PasswordBroker;
use PhpParser\Node\Stmt\TryCatch;
use Illuminate\Support\Facades\Hash as FacadesHash;

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

    public function registerAPI(Request $request)
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

        $token = $user->createToken('auth_token')->plainTextToken;
        
        return response()->json([
            'success' => true,
            'user' => $user,
            'token' => $token,
            'message' => 'User registered successfully.'
        ], 200);
    }

    public function loginAPI(Request $request)
    {
        try {
            $user = User::where('email', '=', $request->input('email'))->firstOrFail();

            if(FacadesHash::check($request->input('password'), $user->password)){
                $token = $user->createToken('auth_token')->plainTextToken;
                return response()->json([
                    'success' => true,
                    'user' => $user,
                    'token' => $token,
                    'message' => 'User logged in successfully.'
                ], 200);
            }


            return response()->json([
                'error' => 'Invalid credentials',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'User not found.'
            ], 404);
        }
    }

    public function logoutAPI(Request $request)
    {
        try {
            $user = User::findOrFail($request->input('user_id'));

            $user->tokens()->delete();

            return response()->json([
                'user' => 'User logged out successfully.',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'User not found.'
            ], 404);
        }
    }
}
