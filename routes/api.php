<?php

use App\Models\WorkingHour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Menu\MenuController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Menu\UserMenuController;
use App\Http\Controllers\Customer\CustomerController;
use App\Http\Controllers\Company\CompanyInfoController;
use App\Http\Controllers\AppSetting\AppSettingController;
use App\Http\Controllers\WorkingHour\WorkingHourController;
use App\Http\Controllers\Calendar\CalendarHolidayController;
use App\Http\Controllers\Customer\CustomerContractController;
use App\Http\Controllers\GeneralSetup\GeneralSetupController;
use App\Http\Controllers\UnitOfMeasure\UnitOfMeasureController;
use App\Http\Controllers\NumberSequence\NumberSequenceController;
use App\Http\Controllers\Calculation\MultiplicationCalculationController;
use App\Http\Controllers\Calculation\OvertimeMultiplicationSetupController;
use App\Http\Controllers\Classification\ClassificationOfTaxPayerController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('menu', [MenuController::class, 'getMenu']);
    Route::get('menu/all/{user_id}', [MenuController::class, 'getAllMenu']);
    Route::get('menu-user-permission/menu/{id}', [UserMenuController::class, 'UserMenuPermission']);

    Route::post('update-menu', [UserMenuController::class, 'UpdateUserMenu']);

    Route::group(['prefix' => 'company'], function () {
        Route::post('create', [CompanyInfoController::class, 'create']);
        Route::get('list', [CompanyInfoController::class, 'list']);
        Route::get('detail/{id}', [CompanyInfoController::class, 'detail']);
        Route::post('update/{id}', [CompanyInfoController::class, 'update']);
        Route::post('delete/{id}', [CompanyInfoController::class, 'delete']);
        Route::post('search', [CompanyInfoController::class, 'search']);
    });

    Route::group(['prefix' => 'number-sequence'], function () {
        Route::post('create', [NumberSequenceController::class, 'create']);
        Route::get('all', [NumberSequenceController::class, 'getAll']);
        Route::get('detail/{id}', [NumberSequenceController::class, 'detail']);
        Route::post('update/{id}', [NumberSequenceController::class, 'update']);
        Route::post('delete/{id}', [NumberSequenceController::class, 'delete']);
        Route::post('search', [NumberSequenceController::class, 'search']);
    });

    Route::group(['prefix' => 'unit-of-measure'], function () {
        Route::post('create', [UnitOfMeasureController::class, 'create']);
        Route::get('all', [UnitOfMeasureController::class, 'list']);
        Route::get('detail/{id}', [UnitOfMeasureController::class, 'detail']);
        Route::post('update/{id}', [UnitOfMeasureController::class, 'update']);
        Route::post('delete/{id}', [UnitOfMeasureController::class, 'delete']);
        Route::post('search', [UnitOfMeasureController::class, 'search']);
    });

    Route::group(['prefix' => 'multiplication-calculation'], function () {
        Route::post('create', [MultiplicationCalculationController::class, 'create']);
        Route::get('all', [MultiplicationCalculationController::class, 'getList']);
        Route::get('detail/{id}', [MultiplicationCalculationController::class, 'detail']);
        Route::post('update/{id}', [MultiplicationCalculationController::class, 'update']);
        Route::post('delete/{id}', [MultiplicationCalculationController::class, 'delete']);
        Route::post('search', [MultiplicationCalculationController::class, 'search']);
    });

    Route::group(['prefix' => 'overtime-multiplication-setup'], function() {
        Route::post('create', [OvertimeMultiplicationSetupController::class, 'create']);
        Route::get('all', [OvertimeMultiplicationSetupController::class, 'list']);
        Route::get('detail/{id}', [OvertimeMultiplicationSetupController::class, 'detail']);
        Route::post('update/{id}', [OvertimeMultiplicationSetupController::class, 'update']);
        Route::post('delete/{id}', [OvertimeMultiplicationSetupController::class, 'delete']);
        Route::post('search', [OvertimeMultiplicationSetupController::class, 'search']);
    });

    Route::group(['prefix' => 'general-setup'], function () {
        Route::post('create', [GeneralSetupController::class, 'create']);
        Route::get('list', [GeneralSetupController::class, 'getAll']);
        Route::get('detail/{id}', [GeneralSetupController::class, 'detail']);
        Route::post('update/{id}', [GeneralSetupController::class, 'update']);
        Route::post('delete/{id}', [GeneralSetupController::class, 'delete']);
        Route::post('search', [GeneralSetupController::class, 'search']);
    });

    Route::group(['prefix' => 'working-hour'], function () {
        Route::post('create', [WorkingHourController::class, 'create']);
        Route::get('all', [WorkingHourController::class, 'getAll']);
        Route::get('detail/{id}', [WorkingHourController::class, 'detail']);
        Route::post('update/{id}', [WorkingHourController::class, 'update']);
        Route::post('delete/{id}', [WorkingHourController::class, 'delete']);
        Route::post('search', [WorkingHourController::class, 'search']);
    });

    // route calendar holiday
    Route::group(['prefix' => 'calendar-holiday'], function () {
        Route::post('create', [CalendarHolidayController::class, 'create']);
        Route::get('list', [CalendarHolidayController::class, 'getList']);
        Route::get('detail/{id}', [CalendarHolidayController::class, 'detail']);
        Route::post('update/{id}', [CalendarHolidayController::class, 'update']);
        Route::post('delete/{id}', [CalendarHolidayController::class, 'delete']);
        Route::post('search', [CalendarHolidayController::class, 'search']);
    });

    // route classification of tax payer
    Route::group(['prefix' => 'classification-of-tax-payer'], function () {
        Route::post('create', [ClassificationOfTaxPayerController::class, 'create']);
        Route::get('list', [ClassificationOfTaxPayerController::class, 'getList']);
        Route::get('detail/{id}', [ClassificationOfTaxPayerController::class, 'detail']);
        Route::post('update/{id}', [ClassificationOfTaxPayerController::class, 'update']);
        Route::post('delete/{id}', [ClassificationOfTaxPayerController::class, 'delete']);
        Route::post('search', [ClassificationOfTaxPayerController::class, 'search']);
    });

    Route::group(['prefix'=> 'app-setting'], function() {
        Route::get('all', [AppSettingController::class, 'getAll']);
        Route::post('create', [AppSettingController::class, 'create']);
        Route::post('update/{id}', [AppSettingController::class, 'update']);
        Route::post('delete/{id}', [AppSettingController::class, 'delete']);
    });

    Route::group(['prefix'=> 'user'], function() {
        Route::get('current', function(){
            return response()->json(Auth::user(), 200);
        });
        Route::post('create', [UserController::class, 'createUser']);
        Route::post('update/{id}', [UserController::class, 'updateUserId']);
        Route::post('delete/{id}', [UserController::class, 'deleteUser']);
        Route::get('list', [UserController::class, 'listUser']);
        Route::get('detail/{id}', [UserController::class, 'detailUser']);
        Route::post('search', [UserController::class, 'searchUser']);
    });

    Route::group(['prefix'=> 'customer'], function() {
        Route::post('create', [CustomerController::class, 'create']);
        Route::post('update/{id}', [CustomerController::class, 'update']);
        Route::post('delete/{id}', [CustomerController::class, 'delete']);
        Route::get('list', [CustomerController::class, 'list']);
        Route::get('detail/{id}', [CustomerController::class, 'detail']);
        Route::post('search', [CustomerController::class, 'search']);
    });

    Route::group(['prefix' => 'customer-contract'], function () {
        Route::post('create', [CustomerContractController::class, 'create']);
        Route::post('update/{id}', [CustomerContractController::class, 'update']);
        Route::post('delete/{id}', [CustomerContractController::class, 'delete']);
        Route::get('list', [CustomerContractController::class, 'list']);
        Route::get('detail/{id}', [CustomerContractController::class, 'detail']);
        Route::post('search', [CustomerContractController::class, 'search']);
    });

    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('update-self', [UserController::class, 'updateUserSelf']);
});

Route::post('login', [AuthController::class, 'login']);
Route::get('validation-error', function () {
    return response()->json([
        "message" => "Validation error"
    ], 401);
});
