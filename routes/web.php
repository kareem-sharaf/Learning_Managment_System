<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DemoController;

use Illuminate\Http\Request;
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
Route::prefix('lessons')->group(function () {
    Route::controller(LessonController::class)->group(function () {
        Route::middleware(['auth:sanctum', 'checkIfTeacher'])->group(function () {
            Route::post('/add', 'add_lesson');
            Route::post('/update', 'update_lesson');
            Route::post('/delete', 'delete_lesson');
        });
        Route::post('/get', 'getLessonsByUnitId');
        Route::post('/getid', 'getLessonById');
    });
});
