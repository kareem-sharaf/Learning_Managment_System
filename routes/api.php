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
use App\Http\Controllers\QuizzesController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\ProgressController;



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API  for your application. These
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
        Route::group(['middleware' => ['auth:sanctum', 'checkIfManager']], function () {
            Route::post('deleteUser', 'deleteUser');
        });
        Route::group(['middleware' => ['auth:sanctum', 'checkIfManagerOrAdmin']], function () {
            Route::get('indexUsers', 'indexUsers');
        });
    });
    Route::controller(UserVerificationController::class)->group(function () {
        Route::group(['middleware' => ['auth:sanctum', 'checkIfManager']], function () {
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
        Route::get('showOne/{category_id}', 'showOne');
        Route::group(['middleware' => ['auth:sanctum', 'CheckIfManagerOrAdminOrTeacher']], function () {
            Route::post('store', 'store');
            Route::post('update/{id}', 'update');
            Route::post('destroy', 'destroy');
            Route::get('showSoftDeleted', 'showSoftDeleted');
        });
        Route::group(['middleware' => ['auth:sanctum', 'checkIfManagerOrAdmin']], function () {
            Route::post('forceDelete', 'forceDelete');
        });
    });
});

//  stages routes
Route::group(['prefix' => 'stage'], function () {
    Route::controller(StageController::class)->group(function () {
        Route::group(['middleware' => ['auth:sanctum', 'checkIfManagerOrAdmin']], function () {
            Route::get('index', 'index');
            Route::post('store', 'store');
            Route::post('update', 'update');
            Route::post('destroy', 'destroy');
        });
        Route::post('search', 'search');
    });
});



//  years routes
Route::group(['prefix' => 'year'], function () {
    Route::controller(YearController::class)->group(function () {
        Route::get('index', 'index');

        Route::group(['middleware' => ['auth:sanctum', 'checkIfManagerOrAdmin']], function () {
            Route::post('store', 'store');
            Route::post('update', 'update');
            Route::post('destroy', 'destroy');
        });
        Route::post('search', 'search');
    });
});

//  role routes
Route::group(['prefix' => 'role'], function () {
    Route::controller(RoleController::class)->group(function () {
        Route::group(['middleware' => ['auth:sanctum', 'checkIfManagerOrAdmin']], function () {
            Route::get('index', 'index');
            Route::post('store', 'store');
            Route::post('update', 'update');
            Route::post('destroy', 'destroy');
        });
    });
});

//  ads routes
Route::group(['prefix' => 'ad'], function () {
    Route::controller(ADController::class)->group(function () {
        Route::get('index', 'index');
        Route::post('show', 'show');
        Route::group(['middleware' => ['auth:sanctum', 'checkIfManagerOrAdmin']], function () {
            Route::post('store', 'store');
            Route::post('update', 'update');
            Route::post('setExpired', 'setExpired');
            Route::post('destroy', 'destroy');
        });
        Route::group(['middleware' => ['auth:sanctum']], function () {
            Route::get('showNewest', 'showNewest');
        });
    });
});

//  fav routes
Route::group(['prefix' => 'fav'], function () {
    Route::controller(FavoriteController::class)->group(function () {
        Route::group(['middleware' => ['auth:sanctum', 'checkIfStudent']], function () {
            Route::get('index', 'index');
            Route::post('toggle', 'toggle');
        });
    });
});

//  bookmark routes
Route::group(['prefix' => 'bookmark'], function () {
    Route::controller(BookmarkController::class)->group(function () {
        Route::group(['middleware' => ['auth:sanctum', 'checkIfStudent']], function () {
            Route::get('index', 'index');
            Route::post('toggle', 'toggle');
        });
    });
});

//  progress routes
Route::group(['prefix' => 'progress'], function () {
    Route::controller(ProgressController::class)->group(function () {
        Route::group(['middleware' => ['auth:sanctum', 'checkIfStudent']], function () {
            Route::get('index', 'indexPerUser');
            Route::post('store', 'store');
            Route::post('destroy', 'destroy');
        });
    });
});

//  subject routes
Route::group(['prefix' => 'subject'], function () {
    Route::controller(SubjectController::class)->group(function () {
        Route::get('show_all_subjects/{category_id}/{year_id?}', 'show_all_subjects');
        Route::get('showOne/{subject_id}', 'showOne');

        Route::middleware('auth:sanctum')->group(function () {
            Route::get('index', 'index');
            Route::group(['middleware' => 'CheckIfManagerOrAdminOrTeacher:sanctum'], function () {
                Route::post('add_subject', 'add_subject');
                Route::post('edit_subject/{subject_id}', 'edit_subject');
                Route::get('delete_subject/{subject_id}', 'delete_subject');
            });
            Route::get('buy_subject', 'buy_subject');
        });
    });
});

//  quiz routes
Route::group(['prefix' => 'quiz'], function () {
    Route::controller(QuizzesController::class)->group(function () {
        Route::post('show_all', 'show_all');
        Route::post('show_all_to_student', 'show_all_to_student');
        Route::post('show_all_to_teacher', 'show_all_to_teacher');
        Route::middleware('auth:sanctum')->group(function () {

            Route::group(['middleware' => 'checkIfStudent:sanctum'], function () {
                Route::post('take_quiz', 'take_quiz');
            });

            Route::group(['middleware' => 'checkIfTeacher:sanctum'], function () {
                Route::get('show_one_to_teacher', 'show_one_to_teacher');
                Route::post('add_quiz', 'add_quiz');
                Route::post('edit_quiz', 'edit_quiz');
                Route::get('delete_quiz/{quiz_id}', 'delete_quiz');
            });

            Route::group(['middleware'], function () {
                Route::post('show_to_all', 'show_to_all');
            });
        });
    });
});

//  subscription routes
Route::group(['prefix' => 'subscription'], function () {
    Route::controller(SubscriptionController::class)->group(function () {
        Route::middleware('auth:sanctum')->group(function () {
            Route::group(['middleware' => 'checkIfStudent:sanctum'], function () {
                Route::post('buy_subject', 'buy_subject');
                Route::get('delete_request', 'delete_request');
                Route::get('show_all_courses_for_student', 'show_all_courses_for_student');
                Route::get('show_one_request_for_student', 'show_one_request_for_student');
            });

            Route::group(['middleware' => 'checkIfTeacher:sanctum'], function () {
                Route::get('show_all_requests_for_teacher', 'show_all_requests_for_teacher');
                Route::get('show_one_request_for_teacher', 'show_one_request_for_teacher');
                Route::post('edit_request', 'edit_request');
                Route::get('delete_request', 'delete_request');
            });
        });
    });
});

//  unit routes
Route::group(['prefix'=> 'unit'], function () {
    Route::controller(UnitsController::class)->group(function () {
        Route::get('show_all_units/{subject_id}', 'show_all_units');
        Route::post('search_to_unit', 'search_to_unit');
        Route::group(['middleware' => ['auth:sanctum', 'CheckIfManagerOrAdminOrTeacher']], function () {
            Route::post('add_unit', 'add_unit');
            Route::post('edit_unit', 'edit_unit');
            Route::post('delete_unit', 'delete_unit');
        });
    });
});


//  profile routes
Route::group(['prefix' => 'profile'], function () {
    Route::controller(ProfileController::class)->group(function () {

        Route::post('teachers_in_category', 'teachers_in_category');
        Route::post('show_one_teacher', 'show_one_teacher');
        Route::post('show_one_student', 'show_one_student');

        Route::get('show_teachers_in_subject', 'show_teachers_in_subject');
        Route::group(['middleware' => 'auth:sanctum'], function () {
            Route::get('deleteProfile', 'deleteProfile');
            Route::group(['middleware' => ['auth:sanctum', 'checkIfManagerOrAdmin']], function () {
                Route::get('show_all_teachers', 'show_all_teachers');
                Route::get('show_all_students', 'show_all_students');
            });
        });
    });
});


//  lessons routes
Route::prefix('lessons')->group(function () {
    Route::controller(LessonController::class)->group(function () {
        Route::middleware(['auth:sanctum', 'CheckIfManagerOrAdminOrTeacher'])->group(function () {
            Route::post('/add', 'add_lesson');
            Route::post('/update', 'update_lesson');
            Route::post('/delete', 'delete_lesson');
        });
        Route::middleware(['auth:sanctum'])->group(function () {
            Route::post('/get', 'getLessonsByUnitId');
            Route::post('/show', 'getLessonById');
        });
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
            Route::post('/teacherReply', 'teacherReply');
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


Route::group(['prefix' => 'feedback'], function () {
    Route::controller(FeedbackController::class)->group(function () {
        Route::group(['middleware' => ['auth:sanctum', 'checkIfManagerOrAdmin']], function () {
            Route::post('index', 'index');
            Route::post('show', 'show');
        });
        Route::group(['middleware' => ['auth:sanctum', 'checkIfStudent']], function () {
            Route::post('store', 'store');
            Route::post('destoy', 'destroy');
        });
    });
});
