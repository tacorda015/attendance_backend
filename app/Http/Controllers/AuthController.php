<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');
        Log::alert('Attempt Login');

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

        Log::info($user->first_name . ' ' . $user->last_name . ': Login');

        return response()->json([
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        try {
            $token =JWTAuth::getToken();

            if (!$token) {
                return response()->json(['error' => 'Token not provided'], 400);
            }

            try {
                // Authenticate the token to get the user
                $user = JWTAuth::parseToken()->authenticate();

                // Log the user's first name (f_name)
                Log::info($user->f_name . ' ' . $user->l_name . ': Logout');
            } catch (JWTException $e) {
                return response()->json(['error' => 'Token expired or invalid'], 401);
            }

            JWTAuth::invalidate($token);

            return response()->json(['message' => 'Successfully logged out']);

        } catch (JWTException $e) {
            return response()->json(['error' => 'Could not invalidate token: ' . $e->getMessage()], 500);
        }
    }

}
