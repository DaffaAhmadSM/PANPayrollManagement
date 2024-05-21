<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\Menu\MenuController;
use App\Http\Controllers\Menu\UserMenuController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('menu', [MenuController::class, 'getMenu']);
    Route::post('update-menu', [UserMenuController::class, 'UpdateUserMenu']);
});

Route::post('login', [UserController::class, 'login']);
