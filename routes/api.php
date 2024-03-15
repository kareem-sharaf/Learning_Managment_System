<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SMSController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\UnitsController;
use App\Http\Controllers\TeachersController;


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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('sendSMS', [SMSController::class, 'sendSMS']);


Route::group(['prefix' => 'auth'], function () {
    Route::controller(AuthController::class)->group(function () {
        Route::post('login', 'login');
        Route::post('register', 'register');

        Route::group(['middleware' => 'auth:sanctum'], function () {
            Route::get('logout', 'logout');
            Route::get('user', 'user');
        });
    });
});






Route::group(['prefix' => 'subject'], function () {
    Route::controller(SubjectController::class)->group(function () {
        Route::post('show_all_subjects', 'show_all_subjects');
        Route::post('search_to_subject', 'search_to_subject');

        Route::group(['middleware' => 'auth:sanctum'], function () {
            Route::post('add_subject', 'add_subject');
            Route::post('add_subject_and_assign_teachers', 'add_subject_and_assign_teachers');
            Route::post('edit_subject/{subject_id}', 'edit_subject');
            Route::delete('delete_subject/{subject_id}', 'delete_subject');

        });
    });
});








Route::group(['prefix' => 'unit'], function () {
    Route::controller(UnitsController::class)->group(function () {
        Route::post('show_all_units', 'show_all_units');
        Route::post('search_to_unit', 'search_to_unit');

        Route::group(['middleware' => 'auth:sanctum'], function () {
            Route::post('add_unit', 'add_unit');
            Route::post('edit_unit/{unit_id}', 'edit_unit');
            Route::delete('delete_unit/{unit_id}', 'delete_unit');

        });
    });
});




Route::group(['prefix' => 'teacher'], function () {
    Route::controller(TeachersController::class)->group(function () {
        Route::post('show_all_teachers', 'show_all_teachers');
        Route::post('search_to_teacher', 'search_to_teacher');

        Route::group(['middleware' => 'auth:sanctum'], function () {
            Route::post('add_teacher', 'add_teacher');
            Route::post('edit_teacher/{teacher_id}', 'edit_teacher');
            Route::delete('delete_teacher/{teacher_id}', 'delete_teacher');

        });
    });
});
