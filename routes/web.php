<?php

use Illuminate\Support\Str;
use App\Exports\TempTimesheetExport;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $rand = Str::random(10);
    $strtime = strtotime('now');
    (new TempTimesheetExport(2))->store($rand . $strtime . '.xlsx', 'local');
});
