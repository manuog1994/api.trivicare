<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\UserProfileResource;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin')->except('registerUserProfile');

    }


    public function indexUserProfile()
    {
        $userProfiles = UserProfile::with('orders')->getOrPaginate();

        return UserProfileResource::collection($userProfiles);
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
            'dni' => 'required|string'
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
                'dni' => $request->dni,
            ]);
    
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
        $user_profile->name = 'deleted';
        $user_profile->lastname = 'deleted';
        $user_profile->address = 'deleted';
        $user_profile->optional_address = 'deleted';
        $user_profile->city = 'deleted';
        $user_profile->state = 'deleted';
        $user_profile->country = 'deleted';
        $user_profile->zipcode = 00000;
        $user_profile->phone = 000000000;
        $user_profile->dni = 'deleted';
        $user_profile->birth_date = '0000-00-00';
        $user_profile->save();;

        return response()->json(null, 204);
    }


    public function destroy(User $user)
    {
        $user_profiles = UserProfile::where('user_id', $user->id)->get();
        
        if($user_profiles->count() > 0){
            foreach ($user_profiles as $user_profile) {
                $user_profile->name = 'deleted';
                $user_profile->lastname = 'deleted';
                $user_profile->address = 'deleted';
                $user_profile->optional_address = 'deleted';
                $user_profile->city = 'deleted';
                $user_profile->state = 'deleted';
                $user_profile->country = 'deleted';
                $user_profile->zipcode = 00000;
                $user_profile->phone = 000000000;
                $user_profile->dni = 'deleted';
                $user_profile->birth_date = '0000-00-00';
                $user_profile->save();
            }
        }

        $user->name = 'deleted';
        $user->email = 'deleted' . $user->id . '@deleted.com';
        $user->password = bcrypt('deleted');
        $user->email_verified_at = null;
        $user->save();
        return response()->json(null, 204);
    }
}
