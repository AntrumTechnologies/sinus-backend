<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

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

Route::get('sinus', [SinusController::class, 'indexExplore'])->middleware('auth:sanctum');
Route::get('sinus/created', [SinusController::class, 'indexCreated'])->middleware('auth:sanctum');
Route::get('sinus/following', [SinusController::class, 'indexFollowing'])->middleware('auth:sanctum');
Route::get('sinus/{id}', [SinusController::class, 'show'])->middleware('auth:sanctum');
Route::put('sinus', [SinusController::class, 'store'])->middleware('auth:sanctum');
Route::put('sinus/delete', [SinusController::class, 'delete'])->middleware('auth:sanctum');

Route::get('sinusvalue/{id}', [SinusValueController::class, 'show'])->middleware('auth:sanctum');
Route::get('sinusvalue/{id}/{limit}', [SinusValueController::class, 'show'])->middleware('auth:sanctum');
Route::put('sinusvalue', [SinusValueController::class, 'store'])->middleware('auth:sanctum');
Route::put('sinusvalue/delete', [SinusValueController::class, 'delete'])->middleware('auth:sanctum');

Route::get('following', [FollowingController::class, 'index'])->middleware('auth:sanctum');
Route::put('follow', [FollowingController::class, 'store'])->middleware('auth:sanctum');
Route::put('unfollow', [FollowingController::class, 'delete'])->middleware('auth:sanctum');

Route::put('log', [LogController::class, 'store'])->middleware('auth:sanctum');

Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'login'])->name('login');
Route::post('logout', [UserController::class, 'logout'])->middleware('auth:sanctum')->name('logout');
Route::get('user', [UserController::class, 'getDetails'])->middleware('auth:sanctum');
Route::post('user/update', [UserController::class, 'updateDetails'])->middleware('auth:sanctum');

Route::post('forgot-password', [UserController::class, 'forgotPassword'])->middleware('guest')->name('password.email');
Route::post('reset-password', [UserController::class, 'resetPassword'])->middleware('guest')->name('password.reset');

Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->middleware(['signed'])->name('verification.verify');

Route::post('/email/resend', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
 
    return response()->json(["success" => ""], 200);
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');