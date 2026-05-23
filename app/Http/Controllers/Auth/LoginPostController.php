<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Auth\AuthController;
use Auth;
use Illuminate\Http\Request;

class LoginPostController extends AuthController
{
    public function __invoke(Request $request)
    {
        $credentials = $request->validate([
            "email"=> "required|email",
            "password"=> "required",
        ]);

        $token = Auth::attempt($credentials);

        dd('token', $token);

        if (!$token) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }
}
