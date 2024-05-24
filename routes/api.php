<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\Company\CompanyInfoController;
use App\Http\Controllers\Menu\MenuController;
use App\Http\Controllers\Menu\UserMenuController;

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
});

Route::post('login', [UserController::class, 'login']);
