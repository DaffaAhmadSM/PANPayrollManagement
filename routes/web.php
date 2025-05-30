<?php

use App\Exports\ExportInvoice;
use App\Exports\ExportInvoiceData;
use App\Exports\InvoiceItemGroup;
use Illuminate\Support\Str;
use App\Models\EmployeeRate;
use App\Models\TempTimeSheet;
use Illuminate\Support\Carbon;
use App\Models\CalendarHoliday;
use App\Models\tempTimesheetLine;
use App\Models\EmployeeDepartment;
use App\Models\EmployeeRateDetail;
use App\Models\InvoiceTotalAmount;
use App\Exports\TempTimesheetExport;
use Illuminate\Support\Facades\Route;
use App\Exports\tempTimesheetExportMI;
use App\Models\Customer;
use App\Models\DailyRate;
use Illuminate\Support\Facades\View;

Route::get('invoice', function () {

    // set timeout to 360
    ini_set('max_execution_time', 520);

    $tempTimesheet = TempTimeSheet::where('random_string', 'AoleD1745490930')->first();

    $customerData = Customer::where('id', $tempTimesheet->customer_id)->first();

    $dataKronos = InvoiceTotalAmount::where('random_string', 'AoleD1745490930')
        ->where('parent_id', 'not regexp', '^NK')
        ->lazy()->groupBy(['parent_id']);

    $dataKronos = $dataKronos->map(function ($item) {
        return $item->chunk(15);
    });

    $dataNonKronos = InvoiceTotalAmount::where('random_string', 'AoleD1745490930')
        ->where('parent_id', 'regexp', '^NK$')
        ->get()->groupBy(['parent_id']);

    $dataNonKronosPlus = InvoiceTotalAmount::where('random_string', 'AoleD1745490930')
        ->where('parent_id', 'regexp', '^NK-')
        ->get()->groupBy(['parent_id']);

    $dataNonKronosPlus = $dataNonKronosPlus->map(function ($item) {
        return $item->chunk(15);
    });

    $dataDailyRate = DailyRate::where('temptimesheet_string', 'AoleD1745490930')->get(['id', 'oracle_job_number', 'temptimesheet_string as random_string', 'grand_total as total_amount', 'parent_id', 'work_hours_total as total_hours', 'string_id']);

    $dataNonKronos = [
        "NK" => $dataNonKronos->collapse(),
        "NK-" => $dataNonKronosPlus,
        "Daily" => $dataDailyRate->collect(),
    ];



    return $dataNonKronos;

    $dateTime = Carbon::now();
    $filename = "INVOICE_" . Carbon::parse($tempTimesheet->from_date)->format("Md") . "-" . Carbon::parse($tempTimesheet->to_date)->format("Md") . "_" . $dateTime->format('YmdHis');

    return (new ExportInvoice($dataKronos, $dataNonKronos, $tempTimesheet, $customerData))->download((string) $filename . '.xlsx');

});
