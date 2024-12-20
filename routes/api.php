<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\AuthController;
use App\Http\Controllers\SupportMessageController;
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

Route::post('/login',[AuthController::Class,'login']);
Route::post('/register',[AuthController::Class,'register']);
Route::post('/verify', [AuthController::class, 'verify']);
Route::post('/verify1', [AuthController::class, 'verify1']);
Route::post('/forgotPassword', [AuthController::class, 'forgotPassword']);
Route::post('/changePassword', [AuthController::class, 'changePassword']);

Route::post('/support_messages', [SupportMessageController::class,'support_messages']);
