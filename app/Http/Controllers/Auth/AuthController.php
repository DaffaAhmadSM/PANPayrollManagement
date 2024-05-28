<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\UserMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    function Login(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required:min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
                'status' => 'error'
            ], 400);
        }

        $user = User::where('email', $request['email'])->first();
        if ($user) {
            $passwordUser = Hash::check($request->password, $user->password);
        }
        if (!$user || !$passwordUser) {
            return response()
                ->json([
                    'message' => 'Email or Password not correct',
                    'status'  => 'error'
                ], 400);
        }
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            "message" => 'Login Success',
            "token" => $token,
        ],200);
    }

    function logout(Request $request) {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'message' => 'Logout success',
            'status' => 'success'
        ]);
    }
}
