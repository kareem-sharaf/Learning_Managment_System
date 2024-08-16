<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Year;
use App\Models\Stage;
use App\Models\SubjectYear;
use App\Models\User;
use App\Models\TeacherSubjectYear;
use App\Models\Category;
use App\Models\Lesson;
use App\Models\Unit;
use App\Models\Subscription;

 use App\Http\Responses\ApiSuccessResponse;
 use App\Http\Responses\ApiErrorResponse;
 use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\TeachersController;
use Illuminate\Support\Facades\DB;
class SubscriptionController extends Controller
{
    //************************************************************************************************* */
    // whene the student add request.
    public function buy_subject(Request $request){
        $user = Auth::user();
        $user_id = $user->id;

        $subject_id = $request->subject_id;
        $subject = Subject::find($subject_id);
        $teacher_subject = TeacherSubjectYear::where('subject_id', $subject_id)->first();
        $teacher_id = $teacher_subject->user_id;
        $SubjectPrice = $subject->price;
        $StudentBalance = $user->balance;
        if ($subject && $teacher_id) {
            // Check if the record already exists
            $exists = $user->subjects2()->wherePivot('subject_id', $subject_id)
                                        ->wherePivot('teacher_id', $teacher_id)
                                        ->exists();

            if (!$exists) {
                if($StudentBalance>=$SubjectPrice){
                $user->subjects2()->attach($subject_id, [
                    'status' => 'done',
                    'teacher_id' => $teacher_id,
                    'user_id' => $user_id,
                    'subject_id' => $subject_id,
                ]);
                $user->balance = $StudentBalance - $SubjectPrice ;
                $user->save();
                $message = "The request added successfully.";
            }else{
                $message = "not enough balance .";
                return response()->json(['message' => 'not enough balance .'], 403);
            }
            } else {
                $message = "The record already exists.";
            }

            return response()->json([
                'message' => $message,
            ]);
        }
    }
    //***********************************************************************************************************************\\
    // public function show_all_requests_for_teacher()
    // {
    //     $user = Auth::user();
    //     $user_id = $user->id;
    //     $subscriptions = Subscription::where('teacher_id', $user_id)->get();
    //     $subscriptionsWithData = [];

    //     foreach ($subscriptions as $subscription) {
    //         $subject = Subject::find($subscription->subject_id);
    //         $user = User::find($subscription->user_id);

    //         $subscriptionData = [
    //             'subscription' => $subscription,
    //             'subject' => $subject,
    //             'user' => $user
    //         ];

    //         $subscriptionsWithData[] = $subscriptionData;
    //     }

    //     return [
    //         'message' => "This is the all requests.",
    //         'Subscriptions' => $subscriptionsWithData,
    //     ];

    // }
    //***********************************************************************************************************************\\
    public function show_all_courses_for_student()
{
    $user = Auth::user();
    $user_id = $user->id;

    $subjects = Subscription::where('user_id', $user_id)
                            ->with('subject')
                            ->get()
                            ->pluck('subject');

    return response()->json([
        'message' => "These are the courses the student is subscribed to.",
        'subjects' => $subjects,
    ]);
}


    //***********************************************************************************************************************\\
 }
