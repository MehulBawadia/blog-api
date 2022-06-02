<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PostsController;
use App\Http\Controllers\Api\UsersController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::middleware(['auth:sanctum'])->group(function () {
    Route::middleware('role:admin-user')->prefix('admin')->group(function () {
        Route::apiResource('users', UsersController::class);
    });

    Route::middleware(['role:admin-user|manager-user|regular-user'])->group(function () {
        Route::apiResource('posts', PostsController::class);
    });
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
