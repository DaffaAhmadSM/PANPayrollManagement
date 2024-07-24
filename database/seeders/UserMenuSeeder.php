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
                "name" => "Working Hours Detail",
                "url" => "/admin/setup/working-hours-detail",
                "order" => 9,
                "level" => 0,
                'parent_id' => 1,
            ],
            [
                "id" => 17,
                "name" => "Classification of Tax Payer",
                "url" => "/admin/setup/classification-of-tax-payer",
                "order" => 10,
                "level" => 0,
                'parent_id' => 1,
            ],
            [
                "id" => 18,
                "name" => "Calendar Holiday",
                "url" => "/admin/setup/calendar-holiday",
                "order" => 11,
                "level" => 0,
                'parent_id' => 1,
            ],
        
        ]; 

        $customerSubmenu = [
            [
                "id" => 19,
                "name" => "Customer Data",
                "url" => "/admin/customer/customer-data",
                "order" => 1,
                "level" => 0,
                'parent_id' => 2,
            ],

            [
                "id" => 20,
                "name" => "Customer Contract",
                "url" => "/admin/customer/customer-contract",
                "order" => 1,
                "level" => 0,
                'parent_id' => 2,
            ],

            [
                "id" => 21,
                "name" => "Customer Calendar Holiday",
                "url" => "/admin/customer/customer-calendar-holiday",
                "order" => 1,
                "level" => 0,
                'parent_id' => 2,
            ],

            [
                "id" => 22,
                "name" => "Customer Rate",
                "url" => "/admin/customer/customer-rate",
                "order" => 1,
                "level" => 0,
                'parent_id' => 2,
            ],
        ];
        

        $invoiceSubmenu = [
            [
                "id" => 23,
                "name" => "Customer TimeSheet",
                "url" => "/admin/invoice/customer-timesheet",
                "order" => 1,
                "level" => 0,
                'parent_id' => 3,
            ],

            [
                "id" => 24,
                "name" => "Customer Invoice",
                "url" => "/admin/invoice/customer-invoice",
                "order" => 1,
                "level" => 0,
                'parent_id' => 3,
            ],
        ];

        $employeeSubmenu = [
            [
                "id" => 25,
                "name" => "Competency",
                "url" => null,
                "order" => 1,
                "level" => 0,
                'parent_id' => 4,
            ],
            [
                "id" => 26,
                "name" => "Employee",
                "url" => null,
                "order" => 1,
                "level" => 0,
                'parent_id' => 4,
            ],
            [
                "id" => 27,
                "name" => "Employment",
                "url" => null,
                "order" => 1,
                "level" => 0,
                'parent_id' => 4,
            ],
            [
                "id" => 28,
                "name" => "Employee Competencies",
                "url" => null,
                "order" => 1,
                "level" => 0,
                'parent_id' => 4,
            ],
            [
                "id" => 29,
                "name" => "Leave",
                "url" => null,
                "order" => 1,
                "level" => 0,
                'parent_id' => 4,
            ],
            [
                "id" => 30,
                "name" => "Position",
                "url" => null,
                "order" => 1,
                "level" => 0,
                'parent_id' => 4,
            ],
        ];

        // employee submenu =  Employee Data, Employee Address, Employee Customer, 
        // employment submenu = Employment type, Employment data

        $employeedataSubmenu = [
            [
                "id" => 31,
                "name" => "Employee Data",
                "url" => "/admin/employee/employee-data",
                "order" => 1,
                "level" => 0,
                'parent_id' => 26,
            ],
            [
                "id" => 32,
                "name" => "Employee Address",
                "url" => "/admin/employee/employee-address",
                "order" => 1,
                "level" => 0,
                'parent_id' => 26,
            ],
            [
                "id" => 33,
                "name" => "Employee Customer",
                "url" => "/admin/employee/employee-customer",
                "order" => 1,
                "level" => 0,
                'parent_id' => 26,
            ],
        ];

        $employmentSubmenu = [
            [
                "id" => 34,
                "name" => "Employment Type",
                "url" => "/admin/employee/employment/employment-type",
                "order" => 1,
                "level" => 0,
                'parent_id' => 27,
            ],
            [
                "id" => 35,
                "name" => "Employment Data",
                "url" => "/admin/employee/employment/employment-data",
                "order" => 1,
                "level" => 0,
                'parent_id' => 27,
            ],
        ];

        $competencySubmenu =[
            [
                "id" => 36,
                "name" => "Certificate Classification",
                "url" => "/admin/employee/competency/certificate-classification",
                "order" => 1,
                "level" => 0,
                'parent_id' => 25,
            ],
            [
                "id" => 37,
                "name" => "Certificate Type",
                "url" => "/admin/employee/competency/certificate-type",
                "order" => 1,
                "level" => 0,
                'parent_id' => 25,
            ],
            [
                "id" => 38,
                "name" => "Education Level",
                "url" => "/admin/employee/competency/education-level",
                "order" => 1,
                "level" => 0,
                'parent_id' => 25,
            ],
            [
                "id" => 39,
                "name" => "Job Skill",
                "url" => "/admin/employee/competency/job-skill",
                "order" => 1,
                "level" => 0,
                'parent_id' => 25,
            ],
            [
                "id" => 40,
                "name" => "Job Responsibility",
                "url" => "/admin/employee/competency/job-responsibility",
                "order" => 1,
                "level" => 0,
                'parent_id' => 25,
            ],
            [
                "id" => 41,
                "name" => "Job Task",
                "url" => "/admin/employee/competency/job-task",
                "order" => 1,
                "level" => 0,
                'parent_id' => 25,
            ],
            
        ];

        $employeeCompetence = [
            [
                "id" => 42,
                "name" => "Employee Certificate",
                "url" => "/admin/employee/employee-competencies/employee-certificates",
                "order" => 1,
                "level" => 0,
                'parent_id' => 28,
            ],

            [
                "id" => 43,
                "name" => "Employee Education",
                "url" => "/admin/employee/employee-competencies/employee-education",
                "order" => 1,
                "level" => 0,
                'parent_id' => 28,
            ],

            [
                "id" => 44,
                "name" => "Employee Skill",
                "url" => "/admin/employee/employee-competencies/employee-skill",
                "order" => 1,
                "level" => 0,
                'parent_id' => 28,
            ],

            [
                "id" => 45,
                "name" => "Employee Professional Experience",
                "url" => "/admin/employee/employee-competencies/employee-professional-experience",
                "order" => 1,
                "level" => 0,
                'parent_id' => 28,
            ],

            [
                "id" => 46,
                "name" => "Employee Project Experience",
                "url" => "/admin/employee/employee-competencies/employee-project-experience",
                "order" => 1,
                "level" => 0,
                'parent_id' => 28,
            ],
        ];

        $leaveSubmenu = [
            [
                "id" => 47,
                "name" => "Leave Category",
                "url" => "/admin/employee/leave/leave-category",
                "order" => 1,
                "level" => 0,
                'parent_id' => 29,
            ],
            [
                "id" => 48,
                "name" => "Leave Adjustment",
                "url" => "/admin/employee/leave/leave-adjustment",
                "order" => 1,
                "level" => 0,
                'parent_id' => 29,
            ],
            [
                "id" => 49,
                "name" => "Leave Request",
                "url" => "/admin/employee/leave/leave-request",
                "order" => 1,
                "level" => 0,
                'parent_id' => 29,
            ],
            [
                "id" => 50,
                "name" => "Leave History",
                "url" => "/admin/employee/leave/leave-history",
                "order" => 1,
                "level" => 0,
                'parent_id' => 29,
            ],
        ];

        $positionSubmenu = [
            [
                "id" => 51,
                "name" => "Grade",
                "url" => "/admin/employee/position/grade",
                "order" => 1,
                "level" => 0,
                'parent_id' => 30,
            ],
            [
                "id" => 52,
                "name" => "Position",
                "url" => "/admin/employee/position/position",
                "order" => 1,
                "level" => 0,
                'parent_id' => 30,
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
            [
                'user_id' => 1,
                'menu_id' => 36,
                'create' => 1,
                'update' => 1,
                'delete' => 1,
            ],
            [
                'user_id' => 1,
                'menu_id' => 37,
                'create' => 1,
                'update' => 1,
                'delete' => 1,
            ],
            [
                'user_id' => 1,
                'menu_id' => 38,
                'create' => 1,
                'update' => 1,
                'delete' => 1,
            ],
            [
                'user_id' => 1,
                'menu_id' => 39,
                'create' => 1,
                'update' => 1,
                'delete' => 1,
            ],
            [
                'user_id' => 1,
                'menu_id' => 40,
                'create' => 1,
                'update' => 1,
                'delete' => 1,
            ],
            [
                'user_id' => 1,
                'menu_id' => 41,
                'create' => 1,
                'update' => 1,
                'delete' => 1,
            ],
            [
                'user_id' => 1,
                'menu_id' => 42,
                'create' => 1,
                'update' => 1,
                'delete' => 1,
            ],
            [
                'user_id' => 1,
                'menu_id' => 43,
                'create' => 1,
                'update' => 1,
                'delete' => 1,
            ],
            [
                'user_id' => 1,
                'menu_id' => 44,
                'create' => 1,
                'update' => 1,
                'delete' => 1,
            ],
            [
                'user_id' => 1,
                'menu_id' => 45,
                'create' => 1,
                'update' => 1,
                'delete' => 1,
            ],
            [
                'user_id' => 1,
                'menu_id' => 46,
                'create' => 1,
                'update' => 1,
                'delete' => 1,
            ],
            [
                'user_id' => 1,
                'menu_id' => 47,
                'create' => 1,
                'update' => 1,
                'delete' => 1,
            ],
            [
                'user_id' => 1,
                'menu_id' => 48,
                'create' => 1,
                'update' => 1,
                'delete' => 1,
            ],
            [
                'user_id' => 1,
                'menu_id' => 49,
                'create' => 1,
                'update' => 1,
                'delete' => 1,
            ],
            [
                'user_id' => 1,
                'menu_id' => 50,
                'create' => 1,
                'update' => 1,
                'delete' => 1,
            ],
            [
                'user_id' => 1,
                'menu_id' => 51,
                'create' => 1,
                'update' => 1,
                'delete' => 1,
            ],
            [
                'user_id' => 1,
                'menu_id' => 52,
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
        Menu::insert($employeedataSubmenu);
        Menu::insert($employmentSubmenu);
        Menu::insert($competencySubmenu);
        Menu::insert($employeeCompetence);
        Menu::insert($leaveSubmenu);
        Menu::insert($positionSubmenu);


        UserMenu::insert($UserMenu);
    }
}
