<?php

use App\Models\Employee;
use App\Models\LeaveHistory;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Schedule::call(
    function () {
       $employee = Employee::all();
       $entitle = [];
        foreach ($employee as $emp) {
            $entitle[] = [
                'employee_id' => $emp->id,
                'name' => $emp->name,
                'amount' => $emp->entitle_leaved_per_month,
                'date' => Carbon::now()->format('Y-m-d'),
                'trans_type' => 'entitle',
            ];
        }

        LeaveHistory::insert($entitle);

    }
)->everyMinute();
