<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DemoController;
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




// //Route::get('/', 'App\Http\Controllers\DemoController@index1');
// Route::get('/edit', 'App\Http\Controllers\DemoController@edit');
// Route::post('/upload-video', [DemoController::class, 'uploadVideo'])->name('upload.video');

Route::post('/upload1', [DemoController::class, 'upload']);
