<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthCheckController extends Controller
{
    public function checkAuth(Request $request)
    {
        if ($request->user()) {
            return response()->json(['authenticated' => true]);
        } else {
            return response()->json(['authenticated' => false]);
        }
    }
}

