<?php

use Illuminate\Support\Str;
use App\Exports\TempTimesheetExport;
use GuzzleHttp\Psr7\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    (new TempTimesheetExport(2))->store('timesheet.xlsx', 'local');
});
