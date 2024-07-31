<?php

namespace App\Http\Controllers\Menu;

use App\Models\Menu;
use App\Models\UserMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class MenuController extends Controller
{
    function getMenu() {

        $user = Auth::user();
        $menu = UserMenu::where('user_id', $user->id)->with('menu:id,name,url,parent_id')->get(['menu_id'])->sortBy('menu_id');
        $transformedMenu = [];
        foreach ($menu as $item) {
            $transformedMenu[] = $item["menu"];
        }
        $menu = $this->recursiveMenu($transformedMenu);
        return response()->json([
            "user" => $user,
            "menu" => $menu
        ], 200);
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

    function getAllmenu($user_id) {
        $menu_unprocessed = Menu::all(["id", "name as content", "parent_id"]);
        $menu = $this->recursiveMenu($menu_unprocessed);
        $usermenu = UserMenu::where('user_id', $user_id)->get(['menu_id', 'create', 'update', 'delete']);
        $menuIds = array_map(function($item) {
            return $item['menu_id'];
        }, $usermenu->toArray());
        $create = [];
        $update = [];
        $delete = [];
        foreach ($usermenu as $item) {
            if ($item['create'] == 1) {
                $create[] = $item['menu_id'];
            }
            if ($item['update'] == 1) {
                $update[] = $item['menu_id'];
            }
            if ($item['delete'] == 1) {
                $delete[] = $item['menu_id'];
            }
        }
        return response()->json([
            'status' => 'success',
            'message' => 'User has permission to access this menu',
            'menu' => $menu,
            'menuChecked' => $menuIds,
            'menuCreate' => $create,
            'menuUpdate' => $update,
            'menuDelete' => $delete
        ], 200);
    }
}
