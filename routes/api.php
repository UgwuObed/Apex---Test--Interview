<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

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


Route::post('/register/admin', [AuthController::class, 'registerAdmin']);
Route::post('/register', [AuthController::class, 'registerUser']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/users', [UserController::class, 'createUser']); 
    Route::get('/user', [UserController::class, 'getUserProfile']);
    Route::get('/admin/users', [UserController::class, 'getUserProfiles']);
    Route::put('/user', [UserController::class, 'updateUserProfile']);
    Route::delete('/users/{id}', [UserController::class, 'deleteUser']); 
});




