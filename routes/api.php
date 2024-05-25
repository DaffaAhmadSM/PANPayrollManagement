<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\Calculation\MultiplicationCalculationController;
use App\Http\Controllers\Calculation\OvertimeMultiplicationSetupController;
use App\Http\Controllers\Menu\MenuController;
use App\Http\Controllers\Menu\UserMenuController;
use App\Http\Controllers\Company\CompanyInfoController;
use App\Http\Controllers\GeneralSetup\GeneralSetupController;
use App\Http\Controllers\NumberSequence\NumberSequenceController;
use App\Http\Controllers\UnitOfMeasure\UnitOfMeasureController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('menu', [MenuController::class, 'getMenu']);
    Route::post('update-menu', [UserMenuController::class, 'UpdateUserMenu']);

    Route::group(['prefix' => 'company'], function () {
        Route::post('create', [CompanyInfoController::class, 'create']);
        Route::get('list', [CompanyInfoController::class, 'list']);
        Route::get('detail/{id}', [CompanyInfoController::class, 'detail']);
        Route::post('update/{id}', [CompanyInfoController::class, 'update']);
        Route::post('delete/{id}', [CompanyInfoController::class, 'delete']);
    });

    Route::group(['prefix' => 'number-sequence'], function () {
        Route::post('create', [NumberSequenceController::class, 'create']);
        Route::get('all', [NumberSequenceController::class, 'getAll']);
        Route::get('detail/{id}', [NumberSequenceController::class, 'detail']);
        Route::post('update/{id}', [NumberSequenceController::class, 'update']);
        Route::post('delete/{id}', [NumberSequenceController::class, 'delete']);
    });

    Route::group(['prefix' => 'unit-of-measure'], function () {
        Route::post('create', [UnitOfMeasureController::class, 'create']);
        Route::get('all', [UnitOfMeasureController::class, 'list']);
        Route::get('detail/{id}', [UnitOfMeasureController::class, 'detail']);
        Route::post('update/{id}', [UnitOfMeasureController::class, 'update']);
        Route::post('delete/{id}', [UnitOfMeasureController::class, 'delete']);
    });

    Route::group(['prefix' => 'multiplication-calculation'], function () {
        Route::post('create', [MultiplicationCalculationController::class, 'create']);
        Route::get('all', [MultiplicationCalculationController::class, 'getList']);
        Route::get('detail/{id}', [MultiplicationCalculationController::class, 'detail']);
        Route::post('update/{id}', [MultiplicationCalculationController::class, 'update']);
        Route::post('delete/{id}', [MultiplicationCalculationController::class, 'delete']);
    });

    Route::group(['prefix' => 'overtime-multiplication-setup'], function() {
        Route::post('create', [OvertimeMultiplicationSetupController::class, 'create']);
        Route::get('all', [OvertimeMultiplicationSetupController::class, 'list']);
        Route::get('detail/{id}', [OvertimeMultiplicationSetupController::class, 'detail']);
        Route::post('update/{id}', [OvertimeMultiplicationSetupController::class, 'update']);
        Route::post('delete/{id}', [OvertimeMultiplicationSetupController::class, 'delete']);
    });

    Route::group(['prefix' => 'general-setup'], function () {
        Route::post('create', [GeneralSetupController::class, 'create']);
        Route::get('all', [GeneralSetupController::class, 'getAll']);
        Route::get('detail/{id}', [GeneralSetupController::class, 'detail']);
        Route::post('update/{id}', [GeneralSetupController::class, 'update']);
        Route::post('delete/{id}', [GeneralSetupController::class, 'delete']);
    });
});

Route::post('login', [UserController::class, 'login']);
