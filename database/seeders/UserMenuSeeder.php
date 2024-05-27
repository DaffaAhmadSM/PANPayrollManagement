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

        $menu = [
            [
                "name" => "Setup",
                "url" => null,
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
                    "name" => "Company",
                    "url" => "/admin/setup/company",
                    "order" => 3,
                    "level" => 0,
                ],
                [
                    "name" => "Number Sequence",
                    "url" => "/admin/setup/sequence",
                    "order" => 4,
                    "level" => 0,
                ],
                [
                    "name" => "Unit of Measure",
                    "url" => "/admin/setup/unit-of-measure",
                    "order" => 5,
                    "level" => 0,
                ],
                [
                    "name" => "General Setup",
                    "url" => "/admin/setup/general-setup",
                    "order" => 6,
                    "level" => 0,
                ],
                [
                    "name" => "Multiplication Calculation",
                    "url" => "/admin/setup/multiplication-calculation",
                    "order" => 7,
                    "level" => 0,
                ],
                [
                    "name" => "Overtime Multiplication Setup",
                    "url" => "/admin/setup/overtime-multiplication-setup",
                    "order" => 8,
                    "level" => 0,
                ],
                [
                    "name" => "Working Hours",
                    "url" => "/admin/setup/working-hours",
                    "order" => 9,
                    "level" => 0,
                ],
                [
                    "name" => "Classification of Tax Payer",
                    "url" => "/admin/setup/classification-of-tax-payer",
                    "order" => 10,
                    "level" => 0,
                ],
                [
                    "name" => "Calendar Holiday",
                    "url" => "/admin/setup/calendar-holiday",
                    "order" => 11,
                    "level" => 0,
                ],
            
        ];

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
            [
                'user_id' => 1,
                'menu_id' => 6,
            ],
            [
                'user_id' => 1,
                'menu_id' => 7,
            ],
            [
                'user_id' => 1,
                'menu_id' => 8,
            ],
            [
                'user_id' => 1,
                'menu_id' => 9,
            ],
            [
                'user_id' => 1,
                'menu_id' => 10,
            ],
            [
                'user_id' => 1,
                'menu_id' => 11,
            ],
        ];

        $updateParent = [
            [
                'id' => 2,
                'parent_id' => 1,
            ],
            [
                'id' => 3,
                'parent_id' => 1,
            ],
            [
                'id' => 4,
                'parent_id' => 1,
            ],
            [
                'id' => 5,
                'parent_id' => 1,
            ],
            [
                'id' => 6,
                'parent_id' => 1,
            ],
            [
                'id' => 7,
                'parent_id' => 1,
            ],
            [
                'id' => 8,
                'parent_id' => 1,
            ],
            [
                'id' => 9,
                'parent_id' => 1,
            ],
            [
                'id' => 10,
                'parent_id' => 1,
            ],
            [
                'id' => 11,
                'parent_id' => 1,
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
