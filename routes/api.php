<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\SinusController;
use App\Http\Controllers\API\SinusValueController;
use App\Http\Controllers\API\UserController;

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

Route::get('sinus', [SinusController::class, 'index']);
Route::get('sinus/{id}', [SinusController::class, 'show']);
Route::put('sinus', [SinusController::class, 'store']);
Route::put('sinus/delete', [SinusController::class, 'delete']);

Route::get('sinusvalue/{id}', [SinusValueController::class, 'show']);
Route::put('sinusvalue', [SinusValueController::class, 'store']);
Route::put('sinusvalue/delete', [SinusValueController::class, 'delete']);

Route::post('register', [UserController::class, 'register'])->middleware('guest');
Route::post('login', [UserController::class, 'login'])->middleware('guest')->name('login');
Route::post('logout', [UserController::class, 'logout'])->middleware('auth')->name('logout');
