<?php

namespace App\Http\Controllers;

use App\Models\StaffDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class StaffAuthController extends Controller
{

    /**
     * Staff login and issue a token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        $staff = StaffDetails::where('email', $fields['email'])->first();

        if (!$staff || !Hash::check($fields['password'], $staff->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $response = [
            'message' => 'Staff logged in successfully.',
            'staff' => $staff,
        ];

        return response()->json($response, 200);
    }
}
