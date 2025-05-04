<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RegistrationController;
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

Route::post('/register', [RegistrationController::class, 'register']);
Route::get('/verify/{token}', [RegistrationController::class, 'verify']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
});
