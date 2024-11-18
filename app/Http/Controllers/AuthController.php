<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;
use Firebase\JWT\JWT;

class AuthController extends Controller
{

    public function socialLogin(Request $request)
    {
        
        // $validator = Validator::make($request->all(), [
        //     'full_name' => 'required|string|max:255',
        //     'email' => 'required|email|max:255',
        //     'google_id' => 'string|nullable',
        //     'facebook_id' => 'string|nullable',
        // ]);

        // if ($validator->fails()) {
        //     return response()->json($validator->errors(), 400);
        // }

        $existingUser = User::where('email', $request->email)->first();

        if ($existingUser) {
            $socialMatch = ($request->has('google_id') && $existingUser->google_id === $request->google_id) ||
                ($request->has('facebook_id') && $existingUser->facebook_id === $request->facebook_id);

            if ($socialMatch) {
                $token = JWTAuth::fromUser($existingUser);
                return response()->json(['access_token' => $token, 'token_type' => 'bearer']);
            } elseif (is_null($existingUser->google_id) && is_null($existingUser->facebook_id)) {
                return response()->json(['message' => 'User already exists. Sign in manually.'], 422);
            } else {
                $existingUser->update([
                    'google_id' => $request->google_id ?? $existingUser->google_id,
                    'facebook_id' => $request->facebook_id ?? $existingUser->facebook_id,
                ]);
                $token = JWTAuth::fromUser($existingUser);
                return response()->json(['access_token' => $token, 'token_type' => 'bearer']);
            }
        }

        $user = User::create([
            'full_name' => $request->full_name,
            'user_name' => Str::lower($request->full_name),
            'email' => $request->email,
            'password' => Hash::make(Str::random(16)),
            'role' => 'MEMBER',
            'google_id' => $request->google_id ?? null,
            'facebook_id' => $request->facebook_id ?? null,
            'verify_email' => false,
            'status' => 'active',
        ]);

        $token = JWTAuth::fromUser($user);
        return response()->json(['access_token' => $token, 'token_type' => 'bearer']);
    }


    public function responseWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60 // Time to live in seconds
        ]);
    }
    public function user()
    {
        return response()->json(auth()->user());
    }

    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }
}
