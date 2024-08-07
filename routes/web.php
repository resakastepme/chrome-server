<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    // return view('welcome');
    return redirect()->to('/shielded/privacyandpolicy');
});

Route::get('/run-storage-link', function () {
    try {
        Artisan::call('storage:link');
        return 'SUCCESS';
    } catch (\Throwable $th) {
        return 'FAILED: ' . $th->getMessage();
    }

});

Route::get('/shielded/privacyandpolicy', function () {
    return view('privacy');
});

Route::get('/get-iconpage', function () {
    $filePath = public_path('img/SHIELDED-PAGE.png');
    return response()->download($filePath, 'SHIELDED-PAGE.png');
});

Route::get('/get-iconon', function () {
    $filePath = public_path('img/SHIELDEDON.png');
    return response()->download($filePath, 'SHIELDEDON.png');
});

Route::get('/get-iconoff', function () {
    $filePath = public_path('img/SHIELDEDOFF.png');
    return response()->download($filePath, 'SHIELDEDOFF.png');
});

Route::get('/get-preventionicon', function () {
    $filePath = public_path('img/STOP.jpg');
    return response()->download($filePath, 'STOP.jpg');
});

// Clear All Caches
Route::get('/clear-all-caches', function () {
    Artisan::call('config:clear');
    Artisan::call('route:clear');
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    return "All caches cleared";
});

Route::get('/test-cors', function () {
    return response()->json(['message' => 'CORS working!']);
});
