<?php

namespace App\Http\Controllers\Auth;

use App\Api\v1\Controllers\ApiController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\PersonalAccessTokenResult;

class AuthApiController extends ApiController
{
    // Connexion
    // public function login(Request $request)
    // {
    //     $request->validate([
    //         'email' => 'required|string|email',
    //         'password' => 'required|string',
    //     ]);

    //     if (!Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
    //         return response()->json(['error' => 'Unauthorized'], 401);
    //     }

    //     $user = Auth::user();
    //     $token = $user->createToken('DaybydayCRM Token')->accessToken;

    //     return response()->json(['token' => $token], 200);
    // }

    public function login(Request $request)
    {
        // Valider les champs d'email et de mot de passe
        $credentials = $request->only('email', 'password');
        
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            // Générer un token d'accès pour l'utilisateur
            $token = $user->createToken('auth_token')->plainTextToken;

            return $this->respondCreated(["token" => $token]);
        }

        return $this->respondError("Invalid credentials", 400);
    }
}


?>