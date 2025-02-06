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

    $tempTimesheet = TempTimeSheet::where('random_string', 'cVBWz1737706275')->first();

    $customerData = Customer::where('id', $tempTimesheet->customer_id)->first();

    $dataKronos = InvoiceTotalAmount::where('random_string', 'cVBWz1737706275')
    ->where('parent_id','not regexp', '^NK')
    ->get()->groupBy(['parent_id']);

    $dataKronos = $dataKronos->map(function($item){
        return $item->chunk(15);
    });

    $dataNonKronos = InvoiceTotalAmount::where('random_string', 'cVBWz1737706275')
    ->where('parent_id','regexp', '^NK')
    ->get()->groupBy(['parent_id']);

    $dataNonKronos = $dataNonKronos->map(function($item){
        return $item->chunk(15);
    });

    return (new ExportInvoice($dataKronos, $dataNonKronos, $tempTimesheet, $customerData))->download('invoice.xlsx');

});