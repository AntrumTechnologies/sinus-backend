<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\FollowingController;
use App\Http\Controllers\API\LogController;
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

Route::get('sinus', [SinusController::class, 'indexExplore'])->middleware('auth');
Route::get('sinus/created', [SinusController::class, 'indexCreated'])->middleware('auth');
Route::get('sinus/following', [SinusController::class, 'indexFollowing'])->middleware('auth');
Route::get('sinus/{id}', [SinusController::class, 'show'])->middleware('auth');
Route::put('sinus', [SinusController::class, 'store'])->middleware('auth');
Route::put('sinus/delete', [SinusController::class, 'delete'])->middleware('auth');

Route::get('sinusvalue/{id}', [SinusValueController::class, 'show'])->middleware('auth');
Route::get('sinusvalue/{id}/{limit}', [SinusValueController::class, 'show'])->middleware('auth');
Route::put('sinusvalue', [SinusValueController::class, 'store'])->middleware('auth');
Route::put('sinusvalue/delete', [SinusValueController::class, 'delete'])->middleware('auth');

Route::get('following', [FollowingController::class, 'index'])->middleware('auth');
Route::put('follow', [FollowingController::class, 'store'])->middleware('auth');
Route::put('unfollow', [FollowingController::class, 'delete'])->middleware('auth');

Route::put('log', [LogController::class, 'store'])->middleware('auth');

Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'login'])->name('login');
Route::post('logout', [UserController::class, 'logout'])->middleware('auth')->name('logout');
