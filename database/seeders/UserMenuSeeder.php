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
        $setup = [
            "id" => 1,
            "name" => "Setup",
            "url" => null,
            "order" => 1,
            "level" => 0,
        ];

        $customerMain = [
          
            "id" => 2,
            "name" => "Customer",
            "url" => null,
            "order" => 1,
            "level" => 0,
           
        ];

        $invoiceMain = [
            "id" => 3,
            "name" => "Invoice",
            "url" => null,
            "order" => 1,
            "level" => 0,
        ];

        $employeeMain = [
            "id" => 4,
            "name" => "Employee Menu",
            "url" => null,
            "order" => 1,
            "level" => 0,
        ];

        $payrollMain = [
            "id" => 5,
            "name" => "Payroll",
            "url" => null,
            "order" => 1,
            "level" => 0,
        ];

        $inventoryMain = [
            "id" => 6,
            "name" => "Inventory",
            "url" => null,
            "order" => 1,
            "level" => 0,
        ];

        $jobVacancy = [
            "id" => 7,
            "name" => "Job Vacancy",
            "url" => null,
            "order" => 1,
            "level" => 0,
        ];

        $setupSubmenu = [
            [
                "id" => 8,
                "name" => "User",
                "url" => "/admin/setup/user",
                "order" => 2,
                "level" => 0,
                'parent_id' => 1,
            ],
            [
                "id" => 9,
                "name" => "Company",
                "url" => "/admin/setup/company",
                "order" => 3,
                "level" => 0,
                'parent_id' => 1,
            ],
            [
                "id" => 10,
                "name" => "Number Sequence",
                "url" => "/admin/setup/sequence",
                "order" => 4,
                "level" => 0,
                'parent_id' => 1,
            ],
            [
                "id" => 11,
                "name" => "Unit of Measure",
                "url" => "/admin/setup/unit-of-measure",
                "order" => 5,
                "level" => 0,
                'parent_id' => 1,
            ],
            [
                "id" => 12,
                "name" => "General Setup",
                "url" => "/admin/setup/general-setup",
                "order" => 6,
                "level" => 0,
                'parent_id' => 1,
            ],
            [
                "id" => 13,
                "name" => "Multiplication Calculation",
                "url" => "/admin/setup/multiplication-calculation",
                "order" => 7,
                "level" => 0,
                'parent_id' => 1,
            ],
            [
                "id" => 14,
                "name" => "Overtime Multiplication Setup",
                "url" => "/admin/setup/overtime-multiplication-setup",
                "order" => 8,
                "level" => 0,
                'parent_id' => 1,
            ],
            [
                "id" => 15,
                "name" => "Working Hours",
                "url" => "/admin/setup/working-hours",
                "order" => 9,
                "level" => 0,
                'parent_id' => 1,
            ],
            [
                "id" => 16,
                "name" => "Classification of Tax Payer",
                "url" => "/admin/setup/classification-of-tax-payer",
                "order" => 10,
                "level" => 0,
                'parent_id' => 1,
            ],
            [
                "id" => 17,
                "name" => "Calendar Holiday",
                "url" => "/admin/setup/calendar-holiday",
                "order" => 11,
                "level" => 0,
                'parent_id' => 1,
            ],
        
        ]; 

        $customerSubmenu = [
            [
                "id" => 18,
                "name" => "Customer Data",
                "url" => "/admin/customer/customer-data",
                "order" => 1,
                "level" => 0,
                'parent_id' => 2,
            ],

            [
                "id" => 19,
                "name" => "Customer Contract",
                "url" => "/admin/customer/customer-contract",
                "order" => 1,
                "level" => 0,
                'parent_id' => 2,
            ],

            [
                "id" => 20,
                "name" => "Customer Calendar Holiday",
                "url" => "/admin/customer/customer-calendar-holiday",
                "order" => 1,
                "level" => 0,
                'parent_id' => 2,
            ],

            [
                "id" => 21,
                "name" => "Customer Rate",
                "url" => "/admin/customer/customer-rate",
                "order" => 1,
                "level" => 0,
                'parent_id' => 2,
            ],
        ];
        

        $invoiceSubmenu = [
            [
                "id" => 22,
                "name" => "Customer TimeSheet",
                "url" => "/admin/invoice/customer-timesheet",
                "order" => 1,
                "level" => 0,
                'parent_id' => 3,
            ],

            [
                "id" => 23,
                "name" => "Customer Invoice",
                "url" => "/admin/invoice/customer-invoice",
                "order" => 1,
                "level" => 0,
                'parent_id' => 3,
            ],
        ];

        $employeeSubmenu = [
            [
                "id" => 24,
                "name" => "Competency",
                "url" => null,
                "order" => 1,
                "level" => 0,
                'parent_id' => 4,
            ],
            [
                "id" => 25,
                "name" => "Employee",
                "url" => null,
                "order" => 1,
                "level" => 0,
                'parent_id' => 4,
            ],
            [
                "id" => 26,
                "name" => "Employment",
                "url" => null,
                "order" => 1,
                "level" => 0,
                'parent_id' => 4,
            ],
            [
                "id" => 27,
                "name" => "Employee Competencies",
                "url" => null,
                "order" => 1,
                "level" => 0,
                'parent_id' => 4,
            ],
            [
                "id" => 28,
                "name" => "Leave",
                "url" => null,
                "order" => 1,
                "level" => 0,
                'parent_id' => 4,
            ],
            [
                "id" => 29,
                "name" => "Position",
                "url" => null,
                "order" => 1,
                "level" => 0,
                'parent_id' => 4,
            ],
        ];

        $competencySubmenu =[
            [
                "name" => "Certificate Classification",
                "url" => "/admin/employee/competency/certificate-classification",
                "order" => 1,
                "level" => 0,
                'parent_id' => 24,
            ],
            [
                "name" => "Certificate Type",
                "url" => "/admin/employee/competency/certificate-type",
                "order" => 1,
                "level" => 0,
                'parent_id' => 24,
            ],
            [
                "name" => "Education Level",
                "url" => "/admin/employee/competency/education-level",
                "order" => 1,
                "level" => 0,
                'parent_id' => 24,
            ],
            [
                "name" => "Job Skill",
                "url" => "/admin/employee/competency/job-skill",
                "order" => 1,
                "level" => 0,
                'parent_id' => 24,
            ],
            [
                "name" => "Job Responsibility",
                "url" => "/admin/employee/competency/job-responsibility",
                "order" => 1,
                "level" => 0,
                'parent_id' => 24,
            ],
            [
                "name" => "Job Task",
                "url" => "/admin/employee/competency/job-task",
                "order" => 1,
                "level" => 0,
                'parent_id' => 24,
            ],
            
        ];

        $UserMenu = [
            [
                'user_id' => 1,
                'menu_id' => 1,
                'create' => 1,
                'update' => 1,
                'delete' => 1
            ],
            [
                'user_id' => 1,
                'menu_id' => 2,
                'create' => 1,
                'update' => 1,
                'delete' => 1
            ],
            [
                'user_id' => 1,
                'menu_id' => 3,
                'create' => 1,
                'update' => 1,
                'delete' => 1
            ],
            [
                'user_id' => 1,
                'menu_id' => 4,
                'create' => 1,
                'update' => 1,
                'delete' => 1
            ],
            [
                'user_id' => 1,
                'menu_id' => 5,
                'create' => 1,
                'update' => 1,
                'delete' => 1
            ],
            [
                'user_id' => 1,
                'menu_id' => 6,
                'create' => 1,
                'update' => 1,
                'delete' => 1
            ],
            [
                'user_id' => 1,
                'menu_id' => 7,
                'create' => 1,
                'update' => 1,
                'delete' => 1
            ],
            [
                'user_id' => 1,
                'menu_id' => 8,
                'create' => 1,
                'update' => 1,
                'delete' => 1
            ],
            [
                'user_id' => 1,
                'menu_id' => 9,
                'create' => 1,
                'update' => 1,
                'delete' => 1
            ],
            [
                'user_id' => 1,
                'menu_id' => 10,
                'create' => 1,
                'update' => 1,
                'delete' => 1
            ],
            [
                'user_id' => 1,
                'menu_id' => 11,
                'create' => 1,
                'update' => 1,
                'delete' => 1
            ],
            [
                'user_id' => 1,
                'menu_id' => 12,
                'create' => 1,
                'update' => 1,
                'delete' => 1
            ],
            [
                'user_id' => 1,
                'menu_id' => 13,
                'create' => 1,
                'update' => 1,
                'delete' => 1
            ],
            [
                'user_id' => 1,
                'menu_id' => 14,
                'create' => 1,
                'update' => 1,
                'delete' => 1
            ],
            [
                'user_id' => 1,
                'menu_id' => 15,
                'create' => 1,
                'update' => 1,
                'delete' => 1
            ],
            [
                'user_id' => 1,
                'menu_id' => 16,
                'create' => 1,
                'update' => 1,
                'delete' => 1
            ],
            [
                'user_id' => 1,
                'menu_id' => 17,
                'create' => 1,
                'update' => 1,
                'delete' => 1
            ],
            [
                'user_id' => 1,
                'menu_id' => 18,
                'create' => 1,
                'update' => 1,
                'delete' => 1
            ],
            [
                'user_id' => 1,
                'menu_id' => 19,
                'create' => 1,
                'update' => 1,
                'delete' => 1
            ],
            [
                'user_id' => 1,
                'menu_id' => 20,
                'create' => 1,
                'update' => 1,
                'delete' => 1
            ],
            [
                'user_id' => 1,
                'menu_id' => 21,
                'create' => 1,
                'update' => 1,
                'delete' => 1
            ],
            [
                'user_id' => 1,
                'menu_id' => 22,
                'create' => 1,
                'update' => 1,
                'delete' => 1,
            ],
            [
                'user_id' => 1,
                'menu_id' => 23,
                'create' => 1,
                'update' => 1,
                'delete' => 1,
            ],
            [
                'user_id' => 1,
                'menu_id' => 24,
                'create' => 1,
                'update' => 1,
                'delete' => 1,
            ],
            [
                'user_id' => 1,
                'menu_id' => 25,
                'create' => 1,
                'update' => 1,
                'delete' => 1,
            ],
            [
                'user_id' => 1,
                'menu_id' => 26,
                'create' => 1,
                'update' => 1,
                'delete' => 1,
            ],
            [
                'user_id' => 1,
                'menu_id' => 27,
                'create' => 1,
                'update' => 1,
                'delete' => 1,
            ],
            [
                'user_id' => 1,
                'menu_id' => 28,
                'create' => 1,
                'update' => 1,
                'delete' => 1,
            ],
            [
                'user_id' => 1,
                'menu_id' => 29,
                'create' => 1,
                'update' => 1,
                'delete' => 1,
            ],
            [
                'user_id' => 1,
                'menu_id' => 30,
                'create' => 1,
                'update' => 1,
                'delete' => 1
            ],
            [
                'user_id' => 1,
                'menu_id' => 31,
                'create' => 1,
                'update' => 1,
                'delete' => 1
            ],
            [
                'user_id' => 1,
                'menu_id' => 32,
                'create' => 1,
                'update' => 1,
                'delete' => 1,
            ],
            [
                'user_id' => 1,
                'menu_id' => 33,
                'create' => 1,
                'update' => 1,
                'delete' => 1,
            ],
            [
                'user_id' => 1,
                'menu_id' => 34,
                'create' => 1,
                'update' => 1,
                'delete' => 1,
            ],
            [
                'user_id' => 1,
                'menu_id' => 35,
                'create' => 1,
                'update' => 1,
                'delete' => 1,
            ],
        ];

        Menu::create($setup);
        Menu::create($customerMain);
        Menu::create($invoiceMain);
        Menu::create($employeeMain);
        Menu::create($payrollMain);
        Menu::create($inventoryMain);
        Menu::create($jobVacancy);


        Menu::insert($setupSubmenu);
        Menu::insert($customerSubmenu);
        Menu::insert($invoiceSubmenu);
        Menu::insert($employeeSubmenu);
        Menu::insert($competencySubmenu);


        UserMenu::insert($UserMenu);
    }
}
