<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\UserMenu;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $UserMenu = [
            [
                'user_id' => 1,
                'menu_id' => 1,
            ],
            [
                'user_id' => 1,
                'menu_id' => 2,
            ],
            [
                'user_id' => 1,
                'menu_id' => 3,
            ],
            [
                'user_id' => 1,
                'menu_id' => 4,
            ],
            [
                'user_id' => 1,
                'menu_id' => 5,
            ],
        ];

        $menu = [
            [
                "name" => "Dashboard",
                "url" => "/dashboard",
                "order" => 1,
                "level" => 0,
            ],
            [
                "name" => "User",
                "url" => "/user",
                "order" => 2,
                "level" => 0,
            ],
            [
                "name" => "Role",
                "url" => "/role",
                "order" => 3,
                "level" => 0,
            ],
            [
                "name" => "Menu",
                "url" => "/menu",
                "order" => 4,
                "level" => 0,
            ],
            [
                "name" => "Permission",
                "url" => "/permission",
                "order" => 5,
                "level" => 0,
            ],
        ];

        $updateParent = [
            [
                'id' => 2,
                'parent_id' => 1,
            ],
            [
                'id' => 3,
                'parent_id' => 2,
            ],
            [
                'id' => 5,
                'parent_id' => 4,
            ],
        ];

        Menu::insert($menu);
        UserMenu::insert($UserMenu);

        foreach ($updateParent as $item) {
            $menu = Menu::find($item['id']);
            $menu->parent_id = $item['parent_id'];
            $menu->save();
        }
    }
}
