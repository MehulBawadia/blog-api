<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AllPostController;
use App\Http\Controllers\Api\AllUserController;
use App\Http\Controllers\Api\UserPostController;
use App\Http\Controllers\Api\ManagerPostController;

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

Route::middleware(['auth:sanctum', 'role:admin-user'])->prefix('admin')->group(function () {
    Route::apiResource('posts', AllPostController::class);
    Route::apiResource('users', AllUserController::class);
});

Route::middleware(['auth:sanctum', 'role:manager-user'])->prefix('manager')->group(function () {
    Route::apiResource('posts', AllPostController::class);
});

Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware(['auth:sanctum', 'role:regular-user'])->prefix('user/posts')->group(function () {
    Route::get('/', [UserPostController::class, 'index']);
    Route::post('/', [UserPostController::class, 'store']);
    Route::get('/{slug}', [UserPostController::class, 'show']);
    Route::put('/{id}', [UserPostController::class, 'update']);
    Route::delete('/{id}', [UserPostController::class, 'destroy']);
});
