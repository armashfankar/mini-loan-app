<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LoanController;


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

// API route for User & Admin authentication
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/admin/login', [AuthController::class, 'adminLogin']);

// Auth Protected Routes
Route::group(['middleware' => ['auth:sanctum']], function () {
    // User & Admin profile
    Route::get('/profile', function(Request $request) {
        return auth()->user();
    });

    // API route for logout User & Admin
    Route::post('/logout', [AuthController::class, 'logout']);

    // API route for loan related requests
    Route::resource('/loan', LoanController::class);
});
