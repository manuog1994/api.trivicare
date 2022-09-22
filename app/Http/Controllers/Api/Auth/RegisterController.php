<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\UserProfileResource;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    // UserProfile


    public function indexUserProfile()
    {
        return UserProfileResource::collection(UserProfile::all());
    }

    public function registerUserProfile(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'name' => 'required|string',
            'lastname' => 'required|string',
            'address' => 'required|string',
            'optional_address' => 'nullable|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'country' => 'required|string',
            'zipcode' => 'required|string',
            'phone' => 'required|string',
            'gender' => 'nullable|string',
            'birth_date' => 'nullable|string',
        ]);

        $user_profile = UserProfile::create([
                'user_id' => $request->user_id,
                'name' => $request->name,
                'lastname' => $request->lastname,
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

    public function showProfile($userId, UserProfile $user_profile)
    {
        $user_profile = UserProfile::where('user_id', $userId)->get();

        return UserProfileResource::make($user_profile);
    }

    public function updateEmail($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 422);
        }

        $user = User::find($id);

        $user->email = $request->email;

        $user->save();

        return response()->json([
            'message' => 'success',
        ]);
    }
    public function updatePassword($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 422);
        }

        if (!Hash::check($request->old_password, $request->user()->password)) {
            return response()->json([
                'message' => 'La contraseña actual no coincide con nuestros registros.',
            ], 422);
        }
        
        $user = User::find($id);

        $user->password = bcrypt($request->password);

        $user->save();

        return response()->json([
            'message' => 'success',
        ]);
    }

    public function deleteProfile(UserProfile $user_profile)
    {
        $user_profile->delete();

        return response()->json(null, 204);
    }


    public function destroy(User $user)
    {
        $user_profiles = UserProfile::where('user_id', $user->id)->get();
        
        foreach ($user_profiles as $user_profile) {
            $user_profile->delete();
        }

        $user->delete();
        return response()->json(null, 204);
    }
}
