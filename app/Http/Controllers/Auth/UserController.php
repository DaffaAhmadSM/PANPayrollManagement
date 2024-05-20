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
            $menu = UserMenu::where('user_id', $user->id)->with('menu:id,name,url,parent_id,level')->get(['menu_id']);
        }
        if (!$user || !$passwordUser) {
            return response()
                ->json([
                    'message' => 'Email or Password not correct',
                    'status'  => 'error'
                ], 400);
        }
        $token = $user->createToken('auth_token')->plainTextToken;
        $transformedMenu = [];
        foreach ($menu as $item) {
            $transformedMenu[] = $item["menu"];
        }

        $menu = $this->recursiveMenu($transformedMenu);


        return response()->json([
            "message" => 'Login Success',
            "token" => $token,
            "menu" => $menu,
        ],200);
    }

    function recursiveMenu($menu, $parent = 0) {
        $result = [];
        foreach ($menu as $item) {
            if ($item['parent_id'] == $parent) {
                $children = $this->recursiveMenu($menu, $item['id']);
                if ($children) {
                    $item['children'] = $children;
                }
                $result[] = $item;
            }
        }
        return $result;
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
}
