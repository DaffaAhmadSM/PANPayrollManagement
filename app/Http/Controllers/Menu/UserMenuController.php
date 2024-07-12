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
            'data' => 'required|array',
            'data.*.menu_id' => 'required|integer',
            'data.*.create' => 'required|in:0,1',
            'data.*.update' => 'required|in:0,1',
            'data.*.delete' => 'required|in:0,1',
            'user_id' => 'required|integer',
        ]);
        
        if($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
                'status' => 'error'
            ], 400);
        }
        try {
            UserMenu::where('user_id', $request->user_id)->delete();
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 'error'
            ], 500);
        }
        

        UserMenu::insert($request->data);

        return response()->json([
            'message' => 'Menu updated successfully',
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
