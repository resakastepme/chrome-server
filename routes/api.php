<?php

use App\Models\ExtUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UrlController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\LookController;
use App\Http\Controllers\TextController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\DomainController;
use App\Http\Controllers\DatasetController;
use App\Http\Controllers\ExtUserController;
use App\Http\Controllers\LogTextController;
use App\Http\Controllers\RiwayatController;

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
    Route::middleware(['auth:sanctum'])->post('/ext-user', [ExtUserController::class, 'validating']);
    Route::middleware(['auth:sanctum'])->post('/change-user', [ExtUserController::class, 'change']);

    //STORE DATA EMAIL
    Route::middleware(['auth:sanctum'])->post('/store-data-email', [EmailController::class, 'storeIDAnalisa']);

    //ANALISA TEXT ✅
    Route::middleware(['auth:sanctum'])->post('/store-analisa-text', [TextController::class, 'storeAnalisaText']);

    //TRANSLATE
    Route::middleware(['auth:sanctum'])->post('/translate', [TextController::class, 'translate']);

    //ANALISA DOMAIN ✅
    Route::middleware(['auth:sanctum'])->post('/store-analisa-domain', [DomainController::class, 'storeAnalisaDomain']);

    // ANALISA URL ✅
    Route::middleware(['auth:sanctum'])->post('/store-analisa-url', [UrlController::class, 'storeAnalisaURL']);

    // ANALISA FILE ✅
    Route::middleware(['auth:sanctum'])->post('/store-analisa-file', [FileController::class, 'storeAnalisaFile']);

    // INTERVAL QUEUED ✅
    Route::middleware(['auth:sanctum'])->post('/analisa-file-queue-check', [FileController::class, 'getAnalyzeFile']);

    // GET FINAL URL ✅
    Route::middleware(['auth:sanctum'])->post('/get-final-url', [FileController::class, 'returnFinalURL']);

    // RIWAYAT
    Route::middleware(['auth:sanctum'])->get('/get-riwayat', [RiwayatController::class, 'index']);

    // RIWAYAT DETAIL
    Route::middleware(['auth:sanctum'])->post('/get-riwayat-detail', [RiwayatController::class, 'riwayatDetail']);
});
