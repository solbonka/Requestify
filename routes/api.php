<?php

use App\Http\Controllers\RequestController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::controller(UserController::class)->group(function () {
    Route::post('signin', 'postSignIn');
    Route::post('signup', 'postSignUp');
    Route::post('logout', 'logout')->middleware('auth:api');
});

Route::post('/requests', [RequestController::class, 'create'])->middleware('auth:api');
Route::get('/requests', [RequestController::class, 'getAll'])->middleware('auth:api');
Route::put('/requests/{id}', [RequestController::class, 'resolve'])->middleware('auth:api');
