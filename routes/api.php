<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SMSController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StageController;
use App\Http\Controllers\YearController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ADController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UnitsController;
use App\Http\Controllers\UserVerificationController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Middleware\checkIfTeacher;
use App\Http\Controllers\CommentsController;
use App\Http\Controllers\Video1;
use App\Http\Controllers\files;
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

//  auth routes
Route::group(['prefix' => 'auth'], function () {
    Route::controller(AuthController::class)->group(function () {
        Route::post('registerWeb', 'registerWeb');
        Route::post('register', 'register');
        Route::post('loginWeb', 'loginWeb');
        Route::post('login', 'login');
        Route::post('reset', 'reset');
        Route::post('encrupt', 'encrupt');
        Route::post('check_user', 'check_user');
        Route::post('check_code', 'check_code');
        Route::post('resendEmail', 'resendEmail');
        Route::post('setPassword', 'setPassword');
        Route::get('indexAddressYears', 'indexAddressYears');


        Route::group(['middleware' => 'auth:sanctum'], function () {
            Route::get('logout', 'logout');
        });
    });
    Route::controller(UserVerificationController::class)->group(function () {
        Route::group(['middleware' => 'auth:sanctum', 'checkIfManager', 'checkIfAdmin'], function () {
            Route::post('createUserWeb', 'createUserWeb');
        });
        Route::post('createUser', 'createUser');
        Route::post('verifyUser', 'verifyUser');
        Route::post('resend_email', 'resend_email');
    });
});

//  category routes
Route::group(['prefix' => 'category'], function () {
    Route::controller(CategoryController::class)->group(function () {
        Route::get('index', 'index');
        Route::get('search', 'search');
        Route::post('show', 'show');
        Route::get('showSoftDeleted', 'showSoftDeleted');
        Route::group(['middleware' => 'auth:sanctum', 'checkIfManager', 'checkIfAdmin'], function () {
            Route::post('store', 'store');
            Route::post('update', 'update');
            Route::post('forceDelete', 'forceDelete');
            Route::post('destroy', 'destroy');
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
        Route::get('showNewest', 'showNewest');
        Route::post('show', 'show');
        Route::group(['middleware' => 'auth:sanctum', 'checkIfManager', 'checkIfAdmin'], function () {
            Route::post('store', 'store');
            Route::post('update', 'update');
            Route::post('setExpired', 'setExpired');
            Route::post('destroy', 'destroy');
        });
    });
});

//  fav routes
Route::group(['prefix' => 'fav'], function () {
    Route::controller(FavoriteController::class)->group(function () {
        Route::get('index', 'index');
        Route::get('show', 'show');
        Route::group(['middleware' => 'auth:sanctum', 'checkIfStudent'], function () {
            Route::post('store', 'store');
            Route::post('search', 'search');
            Route::post('destroy', 'destroy');
        });
    });
});

Route::group(['prefix' => 'subject'], function () {
    Route::controller(SubjectController::class)->group(function () {
        Route::get('show_all_subjects', 'show_all_subjects');
        Route::get('all_subjects_in_year', 'all_subjects_in_year');
        Route::get('show_one_subject', 'show_one_subject');
        Route::get('index', 'index');
        Route::get('search', 'search');
        Route::get('search_in_subjects', 'search_in_subjects');

    Route::middleware('auth:sanctum')->group(function () {
        Route::group(['middleware' => 'checkIfTeacher:sanctum' ] , function(){
            Route::post('add_subject', 'add_subject');
            Route::post('edit_subject', 'edit_subject');
            Route::get('delete_subject/{subject_id}', 'delete_subject');

           });
            Route::get('buy_subject', 'buy_subject');
        });

    });
});









Route::group(['prefix' => 'subscription'], function () {
    Route::controller(SubscriptionController::class)->group(function () {
    Route::group(['middleware' => 'auth:sanctum', 'checkIfStudent'], function () {
        Route::get('buy_subject', 'buy_subject');
        Route::get('delete_request', 'delete_request');
        Route::get('show_all_requests_for_student', 'show_all_requests_for_student');
        Route::get('show_one_request_for_student', 'show_one_request_for_student');
    });
    Route::group(['middleware' => 'auth:sanctum', 'checkIfTeacher'], function () {
        Route::get('show_all_requests_for_teacher', 'show_all_requests_for_teacher');
        Route::get('show_one_request_for_teacher', 'show_one_request_for_teacher');
        Route::post('edit_request', 'edit_request');
        Route::get('delete_request', 'delete_request');

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




Route::group(['prefix' => 'profile'], function () {
    Route::controller(ProfileController::class)->group(function () {
        Route::get('show_one_teacher', 'show_one_teacher');
        Route::get('show_one_student', 'show_one_student');
        Route::get('show_all_teachers', 'show_all_teachers');
        Route::get('show_teachers_in_subject', 'show_teachers_in_subject');
        Route::get('teachers_in_category/{category_id}', 'teachers_in_category');
        Route::get('search_in_teacher', 'search_in_teacher');

        Route::group(['middleware' => 'auth:sanctum', 'checkIfManager','checkIfAdmin'], function () {
        });

    });
});
Route::group(['prefix' => 'lessons'], function () {
    Route::controller(LessonController::class)->group(function () {
        Route::post('/add_lesson', 'add_lesson');
        Route::post('/update_lesson', 'update_lesson');
        Route::post('/delete_lesson', 'delete_lesson');
        Route::get('/get_all_lessons', 'get_all_lessons');
    });
});
Route::group(['prefix' => 'comment'], function () {
    Route::controller(CommentsController::class)->group(function () {
        Route::post('/store', 'store');
        Route::post('/update', 'update');
        Route::post('/destroy', 'destroy');
       
    });
});
Route::group(['prefix' => 'video'], function () {
    Route::controller(Video1::class)->group(function () {
        Route::post('/store', 'store');
        Route::post('/update', 'update');
        Route::post('/destroy', 'destroy');
       
    });
});
Route::group(['prefix' => 'files'], function () {
    Route::controller(files::class)->group(function () {
        Route::post('/store', 'store');
        Route::post('/update', 'update');
        Route::post('/destroy', 'destroy');
       
    });
});
Route::post('/message', [ChatController::class, 'message']);
