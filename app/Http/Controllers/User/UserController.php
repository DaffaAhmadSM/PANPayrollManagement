<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
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

        $all = User::cursorPaginate(10, ['id','name', 'email']);
        return response()->json([
            'message' => 'User created successfully',
            'status' => 'success',
            'data' => $all
        ], 201);
    }

    function deleteUser($id) {
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'message' => 'User not found',
                'status' => 'error'
            ], 404);
        }
        $user->delete();
        $all = User::cursorPaginate(10, ['id','name', 'email']);
        return response()->json([
            'message' => 'User deleted successfully',
            'status' => 'success',
            'data' => $all
        ]);
    }

    function updateUserId(Request $request, $id) {
        $validator = Validator::make($request->all(), [
            'emai' => 'email|unique:users',
            'name' => 'string',
            'password' => 'string:min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
                'status' => 'error'
            ], 400);
        }

        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'message' => 'User not found',
                'status' => 'error'
            ], 404);
        }

        if ($request->email) {
            $user->email = $request->email;
        }
        if ($request->name) {
            $user->name = $request->name;
        }
        if ($request->password) {
            $user->password = Hash::make($request->password);
        }
        $user->save();
        $all = User::cursorPaginate(10, ['id','name', 'email']);
        return response()->json([
            'message' => 'User updated successfully',
            'status' => 'success',
            'data' => $all
        ]);
    }

    function updateUserSelf(){

        $validate = Validator::make(request()->all(), [
            'name' => 'string',
            'password' => 'string|min:6'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validate->errors()->first()
            ], 400);
        }

        if (request()->password) {
            request()->merge([
                'password' => Hash::make(request()->password)
            ]);
        }

        $user = User::find(auth()->user()->id);
       
        $user->update(request()->all());

        return response()->json([
            'message' => 'User updated successfully',
            'status' => 'success'
        ]);
    }

    function listUser() {
        $users = User::cursorPaginate(10, ['id','name', 'email']);
        return response()->json([
            'message' => 'List of users',
            'header' => ['name','email'],
            'data' => $users
        ]);
    }

    function detailUser($id) {
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'message' => 'User not found',
                'status' => 'error'
            ], 404);
        }
        return response()->json([
            'message' => 'User found',
            'data' => $user
        ]);
    }

}
