<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\UserMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    function Login(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
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

    function createUser(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
                'status' => 'error'
            ], 400);
        }

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();
        return response()->json([
            'message' => 'User created successfully',
            'status' => 'success'
        ], 201);
    }

    function listUser() {
        $users = User::paginate(10);
        return response()->json([
            'message' => 'List of users',
            'header' => 'List of users',
            'data' => $users
        ]);
    }
}
