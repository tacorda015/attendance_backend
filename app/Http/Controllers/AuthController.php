<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');

        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('username', $credentials['username'])
                    ->where('is_deleted', 0)
                    ->first();

        if (!$user || $user->password !== $credentials['password']) {
            return response()->json(['error' => 'invalid_credentials'], 401);
        }

        try {
            $customClaims = [
                'exp' => Carbon::now()->addHours(12)->timestamp
                // 'exp' => Carbon::now()->addMinutes(3)->timestamp
            ];
            
            $token = JWTAuth::claims($customClaims)->fromUser($user);

        } catch (JWTException $e) {
            return response()->json(['error' => 'could_not_create_token' . $e->getMessage()], 500);
        }

        return response()->json([
            'token' => $token,
        ]);
    }
}
