<?php

use App\Models\ExtUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DatasetController;
use App\Http\Controllers\ExtUserController;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::prefix('/v1')->group(function () {
    Route::post('/signup', [AuthController::class, 'signup']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware(['auth:sanctum'])->get('/test', [AuthController::class, 'test']);

    Route::middleware(['auth:sanctum'])->post('/insert-dataset', [DatasetController::class, 'store']);

    // EXTENSION USER ROUTE
    Route::middleware(['auth:sanctum'])->post('/ext-user', [ExtUserController::class, 'valid']);
    Route::middleware(['auth:sanctum'])->post('/change-user', [ExtUserController::class, 'change']);
});
