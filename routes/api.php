<?php

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route de connexion pour obtenir un token
Route::post('/login', function (Request $request) {
    // Recherche de l'utilisateur par email
    $user = User::where('email', $request->email)->first();

    // Vérification de la validité du mot de passe
    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json(['message' => 'Identifiants invalides'], 401);
    }

    // Création du token
    $token = $user->createToken('NewAppToken')->plainTextToken;

    return response()->json(['token' => $token]);
});

// Route sécurisée qui retourne les informations de l'utilisateur connecté
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Routes protégées par authentification avec Sanctum
Route::group(['namespace' => 'App\Api\v1\Controllers'], function () {
    Route::group(['middleware' => 'auth:sanctum'], function () {
        // Par exemple, route pour obtenir la liste des utilisateurs
        Route::get('users', ['uses' => 'UserController@index']);
    });
});

