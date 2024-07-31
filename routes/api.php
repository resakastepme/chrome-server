<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UrlController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\TextController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\DomainController;
use App\Http\Controllers\DatasetController;
use App\Http\Controllers\ExtUserController;
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

    Route::get('/test', [AuthController::class, 'test']);

    Route::post('/insert-dataset', [DatasetController::class, 'store']);

    // EXTENSION USER ROUTE
    Route::post('/ext-user', [ExtUserController::class, 'validating']);
    Route::post('/change-user', [ExtUserController::class, 'change']);

    //STORE DATA EMAIL
    Route::post('/store-data-email', [EmailController::class, 'storeIDAnalisa']);

    //ANALISA TEXT ✅
    Route::post('/store-analisa-text', [TextController::class, 'storeAnalisaText']);

    //TRANSLATE
    Route::post('/translate', [TextController::class, 'translate']);

    //ANALISA DOMAIN ✅
    Route::post('/store-analisa-domain', [DomainController::class, 'storeAnalisaDomain']);

    // ANALISA URL ✅
    Route::post('/store-analisa-url', [UrlController::class, 'storeAnalisaURL']);

    // ANALISA FILE ✅
    Route::post('/store-analisa-file', [FileController::class, 'storeAnalisaFile']);

    // STORE FILE DATA
    Route::post('/store-file-data', [FileController::class, 'storeFileData']);

    // INTERVAL QUEUED ✅
    Route::post('/analisa-file-queue-check', [FileController::class, 'getAnalyzeFile']);

    // GET FINAL URL ✅
    Route::post('/get-final-url', [FileController::class, 'returnFinalURL']);

    // RIWAYAT
    Route::get('/get-riwayat', [EmailController::class, 'getRiwayat']);

    // RIWAYAT DETAIL
    Route::post('/get-riwayat-detail', [EmailController::class, 'riwayatDetail']);
});

// TEST CORS
Route::get('/test-cors', function () {
    return response()->json(['message' => 'CORS working!']);
});
