<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SMSController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StageController;
use App\Http\Controllers\YearController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ADController;

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
        });
    });
});

//  stages routes
Route::group(['prefix' => 'stage'], function () {
    Route::controller(StageController::class)->group(function () {
        Route::get('index', 'index');
        Route::post('store', 'store');
        Route::post('search', 'search');
        Route::post('update', 'update');
        Route::post('destroy', 'destroy');
    });
});

//  years routes
Route::group(['prefix' => 'year'], function () {
    Route::controller(YearController::class)->group(function () {
        Route::get('index', 'index');
        Route::post('store', 'store');
        Route::post('search', 'search');
        Route::post('update', 'update');
        Route::post('destroy', 'destroy');
    });
});

//  role routes
Route::group(['prefix' => 'role'], function () {
    Route::controller(RoleController::class)->group(function () {
        Route::get('index', 'index');
        Route::post('update', 'update');
    });
});

//  ads routes
Route::group(['prefix' => 'ad'], function () {
    Route::controller(ADController::class)->group(function () {
        Route::get('index', 'index');
        Route::post('show', 'show');
        Route::post('store', 'store');
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
        Route::get('show_one_teacher/{teacher_id}', 'show_one_teacher');
        Route::post('show_all_teachers', 'show_all_teachers');
        Route::get('show_stage_teachers/{stage_id}', 'show_stage_teachers');
        Route::get('show_year_teachers/{year_id}', 'show_year_teachers');

        Route::post('search_to_teacher', 'search_to_teacher');

        Route::group(['middleware' => 'auth:sanctum'], function () {
            Route::post('add_teacher', 'add_teacher');
            Route::post('add_teacher_and_assign_subjects', 'add_teacher_and_assign_subjects');
            Route::post('edit_teacher/{teacher_id}', 'edit_teacher');
            Route::delete('delete_teacher/{teacher_id}', 'delete_teacher');

        });
    });
});
