<?php

namespace App\Http\Controllers;

use App\Models\User;
use Firebase\JWT\JWT;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class AuthController extends Controller
{

    public function signup(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email',
            'location' => 'required|string|max:255',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:ADMIN,OWNER,USER',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:10240',
        ]);
        $imagePath = null;
        if ($request->has('image')) {
            $image = $request->file('image');
            $path = $image->store('profile_images', 'public');
            $imagePath = asset('storage/' . $path);
        }

        $otp = rand(100000, 999999);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'location' => $validated['location'],
            'password' => bcrypt($validated['password']),
            'role' => $validated['role'],
            'image' => $imagePath,
            'otp' => $otp,
        ]);

        try {
            Mail::to($user->email)->send(new OTPVerification($otp));
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }

        $message = match ($user->role) {
            'ADMIN' => 'Welcome Admin! Please verify your email.',
            'OWNER' => 'Welcome Business Owner! Please verify your email.',
            default => 'Welcome User! Please verify your email.',
        };

        return response()->json(['message' => $message], 200);
    }

    //social login
    public function socialLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

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
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make(Str::random(16)),
            'role' => 'USER',
            'google_id' => $request->google_id ?? null,
            'facebook_id' => $request->facebook_id ?? null,
            'location' => $request->location ?? null,
            'verify_email' => true,
            'status' => 'active',
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
        ]);
    }

    //login
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if ($token = Auth::guard('api')->attempt($credentials)) {
            $user = Auth::guard('api')->user();



            return response()->json([
                'access_token' => $token,
                'token_type' => 'bearer',

                'email' => $user->email,
                'role' => $user->role,
                'email_verified_at' => $user->email_verified_at,
            ]);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function responseWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60, // Time to live in seconds
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
