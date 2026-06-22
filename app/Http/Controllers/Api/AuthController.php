<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Kreait\Firebase\Contract\Auth as FirebaseAuth;

class AuthController extends Controller
{
    protected FirebaseAuth $firebaseAuth;

    public function __construct(FirebaseAuth $firebaseAuth)
    {
        $this->firebaseAuth = $firebaseAuth;
    }

    public function login(Request $request)
    {
        $request->validate([
            'firebase_token' => 'required',
        ]);

        try {
            $verifiedIdToken = $this->firebaseAuth->verifyIdToken(
                $request->firebase_token,
                false,
                120,
            );
            $firebaseUid = $verifiedIdToken->claims()->get('sub');

            $firebaseUser = $this->firebaseAuth->getUser($firebaseUid);
            $email = $firebaseUser->email;
            $name = $firebaseUser->displayName ?? explode('@', $email)[0];

            $user = User::where('email', $email)->first();

            if (! $user) {
                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'firebase_uid' => $firebaseUid,
                    'password' => Hash::make(Str::random(16)),
                    'role' => 'staf',
                ]);
            } else {
                if (! $user->firebase_uid) {
                    $user->update(['firebase_uid' => $firebaseUid]);
                }
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Login berhasil',
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user,
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'message' => 'Autentikasi Firebase gagal: '.$e->getMessage(),
                ],
                401,
            );
        }
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Berhasil logout',
        ]);
    }
}
