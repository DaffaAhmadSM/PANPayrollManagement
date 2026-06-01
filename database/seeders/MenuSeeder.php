<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\UserMenu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Menu definitions. Add entries here to register new menus.
     *
     * Each entry: [id, name, url, parent_id]
     * - id:        Fixed ID (check menus table for next available).
     * - name:      Display name.
     * - url:       Frontend route, or null for grouping menus.
     * - parent_id: Parent menu ID, or null for top-level.
     */
    protected array $menus = [
        [52, 'Employee Rate', '/admin/employee/employee-rate', 26],
    ];

    /**
     * Users to assign all new menus to with full CRUD permissions.
     */
    protected array $assignToUsers = [1];

    /**
     * Run the database seeds.
     *
     * Idempotent — safe to run multiple times without rollback.
     */
    public function run(): void
    {
        $menuIds = [];

        foreach ($this->menus as [$id, $name, $url, $parentId]) {
            $menu = Menu::firstOrCreate(
                ['id' => $id],
                [
                    'name' => $name,
                    'url' => $url,
                    'icon' => null,
                    'parent_id' => $parentId,
                    'order' => 1,
                    'level' => 0,
                ],
            );

            $menuIds[] = $menu->id;
        }

        foreach ($this->assignToUsers as $userId) {
            foreach ($menuIds as $menuId) {
                UserMenu::firstOrCreate(
                    [
                        'user_id' => $userId,
                        'menu_id' => $menuId,
                    ],
                    [
                        'create' => 1,
                        'update' => 1,
                        'delete' => 1,
                    ],
                );
            }
        }
    }
}
