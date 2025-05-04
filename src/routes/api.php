<?php

use App\Presentation\Controllers\AuthController;
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

Route::post('/register', [AuthController::class, 'register']);
Route::get('/verify/{token}', [AuthController::class, 'verify']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
});
