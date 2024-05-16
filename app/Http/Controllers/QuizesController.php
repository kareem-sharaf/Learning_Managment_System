<?php

namespace App\Http\Controllers;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Year;
use App\Models\Stage;
use App\Models\SubjectYear;

use App\Http\Responses\ApiSuccessResponse;
use App\Http\Responses\ApiErrorResponse;

use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\TeachersController;

use Illuminate\Http\Request;

class QuizesController extends Controller
{
    public function show_all_quizes_in_class(Request $request)
  {
      $class_id = $request->class_id;
      $quizes = Quize::where('class_id', $class_id)
      ->get();
      return new ApiSuccessResponse(
        'this is the all subjects in the class.',
        $subject,
       201,
    );
  }
}
