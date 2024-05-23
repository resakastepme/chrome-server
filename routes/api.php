<?php

use App\Models\ExtUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\DatasetController;
use App\Http\Controllers\ExtUserController;
use App\Http\Controllers\LogTextController;

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

    //STORE DATA EMAIL
    Route::middleware(['auth:sanctum'])->post('/store-data-email', [EmailController::class, 'storeIDAnalisa']);

    //ANALISA TEXT
    Route::middleware(['auth:sanctum'])->post('/store-analisa-text', [EmailController::class, 'storeAnalisaText']);

    //ANALISA DOMAIN
    Route::middleware(['auth:sanctum'])->post('/store-analisa-domain', [EmailController::class, 'storeAnalisaDomain']);

    // ANALISA URL
    Route::middleware(['auth:sanctum'])->post('/store-analisa-url', [EmailController::class, 'storeAnalisaURL']);
});
