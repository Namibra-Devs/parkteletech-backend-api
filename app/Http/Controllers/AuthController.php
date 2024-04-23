<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register a new user and issue a token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request) {
        try {
            $fields = $request->validate([
                'name' => 'required|string',
                'email' => 'required|string|email|unique:users,email',
                'password' => 'required|string|confirmed'
            ]);

            $user = User::create([
                'name' => $fields['name'],
                'email' => $fields['email'],
                'password' => bcrypt($fields['password'])
            ]);

            $token = $user->createToken('myapptoken')->plainTextToken;

            $response = [
                'message' => 'User successfully registered.',
                'user' => $user,
                'token' => $token
            ];

            return response()->json($response, 201);

        } catch (ValidationException $e) {
            if (isset($e->errors()['email'])) {
                return response()->json([
                    'message' => 'User already exists with this email. Please use a different email address.'
                ], 409);
            }

            throw $e;
        }

    }

    /**
     * Admin login and issue a token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function adminLogin(Request $request) {
        $fields = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        $user = User::where('email', $fields['email'])->first();

        if(!$user || !Hash::check($fields['password'], $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            'message' => 'Admin logged in successfully.',
            'user' => $user,
            'token' => $token
        ];

        return response()->json($response, 200);
    }

    /**
     * Logout the user and revoke all tokens.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request) {
        auth()->user()->tokens()->delete();

        return response()->json(['message' => 'Successfully logged out'], 200);
    }
}
