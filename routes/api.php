<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SMSController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StageController;
use App\Http\Controllers\YearController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ADController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UnitsController;
use App\Http\Controllers\UserVerificationController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\CommentsController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\FilesController;
use App\Http\Controllers\QuizesController;

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
        Route::group(['middleware' => 'auth:sanctum', 'checkIfManager:sanctum', 'checkIfAdmin:sanctum'], function () {
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
        Route::group(['middleware' => 'auth:sanctum', 'checkIfManager:sanctum', 'checkIfAdmin:sanctum'], function () {
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
        Route::group(['middleware' => 'auth:sanctum', 'checkIfManager:sanctum', 'checkIfAdmin:sanctum'], function () {
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
        Route::group(['middleware' => 'auth:sanctum', 'checkIfStudent:sanctum'], function () {
            Route::get('index', 'index');
            Route::post('toggle', 'toggle');
        });
    });
});

//  fav routes
Route::group(['prefix' => 'bookmark'], function () {
    Route::controller(BookmarkController::class)->group(function () {
        Route::group(['middleware' => 'auth:sanctum', 'checkIfStudent:sanctum'], function () {
            Route::get('index', 'index');
            Route::post('toggle', 'toggle');
        });
    });
});

//  subject routes
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
            Route::post('delete_subject', 'delete_subject');

           });
        });
    });
});


//  quiz routes
Route::group(['prefix' => 'quiz'], function () {
    Route::controller(QuizesController::class)->group(function () {
        Route::middleware('auth:sanctum')->group(function () {
            Route::group(['middleware' => 'checkIfTeacher:sanctum'], function () {
                Route::post('add_quiz', 'add_quiz');
                Route::post('edit_quiz', 'edit_quiz');
                Route::post('delete_quiz', 'delete_quiz');
                Route::post('show_all_to_teacher', 'show_all_to_teacher');
            });
        });
            Route::middleware('auth:sanctum','checkIfStudent')->group(function () {
                Route::post('show_all_to_student', 'show_all_to_student');
                Route::post('take_quiz', 'take_quiz');

        });
    });
});

//  subscription routes
Route::group(['prefix' => 'subscription'], function () {
    Route::controller(SubscriptionController::class)->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        Route::group(['middleware' => 'checkIfStudent:sanctum' ] , function(){
            Route::post('buy_subject', 'buy_subject');
            Route::get('show_all_requests_for_student', 'show_all_requests_for_student');
           });
        });
        Route::middleware('auth:sanctum')->group(function () {
            Route::group(['middleware' => 'checkIfTeacher:sanctum' ] , function(){
                Route::get('show_all_requests_for_teacher', 'show_all_requests_for_teacher');
               });
            });
});
});


//  unit routes

Route::group(['prefix' => 'unit'], function () {
    Route::controller(UnitsController::class)->group(function () {
        Route::post('search_to_unit', 'search_to_unit');

        Route::group(['middleware' => 'auth:sanctum'], function () {
            Route::post('add_unit', 'add_unit');
            Route::post('edit_unit', 'edit_unit');
            Route::post('delete_unit', 'delete_unit');
        });
        Route::middleware('auth:sanctum')->group(function () {
            Route::group(['middleware'] , function(){
                Route::post('show_all_units', 'show_all_units');
               });
            });
    });
});



//  profile routes
Route::group(['prefix' => 'profile'], function () {
    Route::controller(ProfileController::class)->group(function () {
        Route::get('show_one_teacher', 'show_one_teacher');
        Route::get('show_one_student', 'show_one_student');
        Route::get('show_all_teachers', 'show_all_teachers');
        Route::get('show_teachers_in_subject', 'show_teachers_in_subject');
        Route::get('teachers_in_category/{category_id}', 'teachers_in_category');
        Route::get('search_in_teacher', 'search_in_teacher');
        Route::group(['middleware' => 'auth:sanctum', 'checkIfManager', 'checkIfAdmin'], function () {
        });
    });
});

//  lessons routes


Route::prefix('lessons')->group(function () {
    Route::controller(LessonController::class)->group(function () {
        Route::middleware(['auth:sanctum', 'checkIfTeacher'])->group(function () {            Route::post('/add', 'add_lesson');
            Route::post('/update', 'update_lesson');
            Route::post('/delete', 'delete_lesson');
            Route::post('/add_lesson', 'add_lesson');
        });
        Route::post('/get', 'getLessonsByUnitId');
        Route::post('/getid', 'getLessonById');
    });
});


//  comment routes


Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('comment')->group(function () {
        Route::controller(CommentsController::class)->group(function () {
            Route::post('/store', 'store');
            Route::post('/update', 'update');
            Route::post('/destroy', 'destroy');
            Route::get('/getComments', 'getComments');
        });
    });
});

//  video routes

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('video')->group(function () {
        Route::controller(VideoController::class)->group(function () {
            Route::post('/store', 'store');
            Route::post('/update', 'update');
            Route::post('/destroy', 'destroy');
        });
    });
});


//  files routes
Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('files')->group(function () {
        Route::controller(FilesController::class)->group(function () {
            Route::post('/store', 'store');
            Route::post('/update', 'update');
            Route::post('/destroy', 'destroy');
        });
    });
});


//  message routes

Route::group(['prefix' => 'message'], function () {
    Route::controller(MessageController::class)->group(function () {
        Route::post('/send', 'sendmessage');
        Route::post('/update', 'updateMessage');
        Route::post('/destroy', 'deleteMessage');
    });
});
