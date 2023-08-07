<?php

namespace App\Http\Controllers\Api\Expo;

use App\Models\ExpoToken;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ExpoTokenController extends Controller
{
    public function store(Request $request)
    {
        // Almacenar token en la base de datos
        $token = $request->input('token');
        
        
        ExpoToken::create([
            'token' => $token,
        ]);

        return response()->json(['message' => 'Token saved successfully']);
    }
}
