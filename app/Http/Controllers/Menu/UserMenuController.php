<?php

namespace App\Http\Controllers\Menu;

use App\Models\UserMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserMenuController extends Controller
{
    function UpdateUserMenu(Request $request) {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'menu_id' => 'required|array',
        ]);
        
        if($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
                'status' => 'error'
            ], 400);
        }

        $user_id = $request->user_id;
        UserMenu::where('user_id', $user_id)->delete();

        $menus = array();

        foreach ($request->menu_id as $menu) {
            $menus[] = [
                "user_id" => $user_id,
                "menu_id" => $menu
            ];
        }

        UserMenu::insert($menus);

        return response()->json([
            'message' => 'Menu added successfully',
            'status' => 'success'
        ], 200);
    }

    function UserMenuPermission($menuid) {
        $user = Auth::user();
        $menu = UserMenu::where('user_id', $user->id)->where('menu_id', $menuid)->first(['create', 'update', 'delete']);
        if ($menu) {
            return response()->json([
                'status' => 'success',
                'message' => 'User has permission to access this menu',
                'permission' => $menu
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'User does not have permission to access this menu'
            ], 403);
        }
    }

}
