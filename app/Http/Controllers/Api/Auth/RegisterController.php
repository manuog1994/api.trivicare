<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserProfileResource;

class RegisterController extends Controller
{
    public function index()
    {
        return UserResource::collection(User::all());
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        return UserResource::make($user);
    }

    public function show(User $user)
    {
        return UserResource::make($user);
    }


    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|confirmed',
        ]);
        
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);
        
        return UserResource::make($user);
    }



    // UserProfile


    public function indexUserProfile()
    {
        return UserProfileResource::collection(UserProfile::all());
    }

    public function registerUserProfile(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'address' => 'required|string',
            'optional_address' => 'nullable|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'country' => 'required|string',
            'zipcode' => 'required|string',
            'phone' => 'required|string',
            'gender' => 'required|string',
            'birth_date' => 'required|string',
        ]);
        // $header = $request->header('Authorization');
        // $token = str_replace('Bearer', '', $header);
        // $access_token = AccessToken::where('access_token', $token)->first();

        // $user = User::find($access_token->user_id);

        $user_profile = UserProfile::create([
                'user_id' => $request->user_id,
                'address' => $request->address,
                'optional_address' => $request->optional_address,
                'city' => $request->city,
                'state' => $request->state,
                'country' => $request->country,
                'zipcode' => $request->zipcode,
                'phone' => $request->phone,
                'gender' => $request->gender,
                'birth_date' => $request->birth_date,
            ]);
    
        return UserProfileResource::make($user_profile);
    }

    public function showUserProfile(UserProfile $user_profile)
    {
        return UserProfileResource::make($user_profile);
    }

    public function updateUserProfile(Request $request, UserProfile $user_profile)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'address' => 'required|string',
            'optional_address' => 'nullable|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'country' => 'required|string',
            'zipcode' => 'required|string',
            'phone' => 'required|string',
            'gender' => 'required|string',
            'birth_date' => 'required|string',
        ]);

        $user_profile->update($request->all());

    
        return UserProfileResource::make($user_profile);
    }

    public function destroy(User $user, UserProfile $user_profile)
    {
        $user->delete();
        $user_profile->delete();
        return response()->json(null, 204);
    }
}
