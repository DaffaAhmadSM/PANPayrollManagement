<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeRateController;

Route::group(['middleware' => ['auth:sanctum']], function () {

    Route::group(["prefix" => "employee-rate"], function () {
        Route::post("import-from-excel", [EmployeeRateController::class, "importRatesFromExcelNew"]);
    });

});
