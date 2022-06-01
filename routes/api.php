<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\UserController;
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

Route::middleware('auth:sanctum')->prefix('admin/users')->group(function () {
    Route::get('/', [UserController::class, 'index']);
    Route::post('/', [UserController::class, 'store']);
    Route::get('/{id}', [UserController::class, 'show']);
    Route::put('/{id}', [UserController::class, 'update']);
    Route::delete('/{id}', [UserController::class, 'destroy']);
});

Route::middleware('auth:sanctum')->prefix('admin/posts')->group(function () {
    Route::get('/', [PostController::class, 'index']);
    Route::post('/', [PostController::class, 'store']);
    Route::get('/{slug}', [PostController::class, 'show']);
    Route::put('/{id}', [PostController::class, 'update']);
    Route::delete('/{id}', [PostController::class, 'destroy']);
});

Route::middleware('auth:sanctum')->prefix('manager/posts')->group(function () {
    Route::get('/', [ManagerPostController::class, 'index']);
    Route::post('/', [ManagerPostController::class, 'store']);
    Route::get('/{slug}', [ManagerPostController::class, 'show']);
    Route::put('/{id}', [ManagerPostController::class, 'update']);
    Route::delete('/{id}', [ManagerPostController::class, 'destroy']);
});

Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->prefix('user/posts')->group(function () {
    Route::get('/', [UserPostController::class, 'index']);
    Route::post('/', [UserPostController::class, 'store']);
    Route::get('/{slug}', [UserPostController::class, 'show']);
    Route::put('/{id}', [UserPostController::class, 'update']);
    Route::delete('/{id}', [UserPostController::class, 'destroy']);
});
