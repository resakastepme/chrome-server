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
    return redirect()->to('/index');
});
Route::get('/index', function () {
    return view('index');
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
