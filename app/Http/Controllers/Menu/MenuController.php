<?php

namespace App\Http\Controllers\Menu;

use App\Models\UserMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class MenuController extends Controller
{
    function getMenu() {

        $user = Auth::user();
        $menu = UserMenu::where('user_id', $user->id)->with('menu:id,name,url,parent_id')->get(['menu_id']);
        $transformedMenu = [];
        foreach ($menu as $item) {
            $transformedMenu[] = $item["menu"];
        }
        $menu = $this->recursiveMenu($transformedMenu);
        return response()->json([
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
}
