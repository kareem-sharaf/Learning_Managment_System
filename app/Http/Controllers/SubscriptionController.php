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
    public function buy_subject(Request $request)
    {
        $user = Auth::user();
        $user_id = $user->id;
        $subject_id = $request->query('subject_id');

        $subject = Subject::find($subject_id);
        $teacher_id = TeacherSubjectYear::where('subject_id', $subject_id)->first();
        $user_id = $teacher_id->user_id;
        if($subject && $teacher_id){
            $user->subjects2()->attach($subject->id, ['status' => 'attended', 'teacher_id' =>$user_id]);
            $message = "The request added successfully.";
            return response()->json([
                 'message' => $message,
            ]);
        }
    }
    //***********************************************************************************************************************\\
    public function show_all_requests_for_teacher()
    {
        $user = Auth::user();
        $user_id = $user->id;
        $subscriptions = Subscription::where('teacher_id', $user_id)->where('status','attended')->get();
        $DoneSubscriptions = Subscription::where('teacher_id', $user_id)->where('status','done')->get();
        $subscriptionsWithData = [];
        $DonesubscriptionsWithData = [];

        foreach ($subscriptions as $subscription) {
            $subject = Subject::find($subscription->subject_id);
            $user = User::find($subscription->user_id);

            $subscriptionData = [
                'subscription' => $subscription,
                'subject' => $subject,
                'user' => $user
            ];

            $subscriptionsWithData[] = $subscriptionData;
        }
        foreach ($DoneSubscriptions as $DoneSubscription) {
                    $subject = Subject::find($DoneSubscription->subject_id);
                    $user = User::find($DoneSubscription->user_id);

                    $DoneSubscription = [
                        'subscription' => $DoneSubscription,
                        'subject' => $subject,
                        'user' => $user
                    ];

                    $DonesubscriptionsWithData[] = $DoneSubscription;
                }
        return [
            'message' => "This is the all requests.",
            'Attended Subscriptions' => $subscriptionsWithData,
            'Done Subscriptions' => $DonesubscriptionsWithData
        ];

    }
    //***********************************************************************************************************************\\
    public function show_all_requests_for_student()
    {
        $user = Auth::user();
        $user_id = $user->id;
        $subscriptions = Subscription::where('user_id', $user_id)->where('status','attended')->get();
        $DoneSubscriptions = Subscription::where('user_id', $user_id)->where('status','done')->get();
        $subscriptionsWithData = [];
        $DonesubscriptionsWithData = [];

        foreach ($subscriptions as $subscription) {
            $subject = Subject::find($subscription->subject_id);
            $user = User::find($subscription->teacher_id);

            $subscriptionData = [
                'subscription' => $subscription,
                'subject' => $subject,
                'user' => $user
            ];

            $subscriptionsWithData[] = $subscriptionData;
        }
        foreach ($DoneSubscriptions as $DoneSubscription) {
                    $subject = Subject::find($DoneSubscription->subject_id);
                    $user = User::find($DoneSubscription->user_id);

                    $DoneSubscription = [
                        'subscription' => $DoneSubscription,
                        'subject' => $subject,
                        'user' => $user
                    ];

                    $DonesubscriptionsWithData[] = $DoneSubscription;
                }
        return [
            'message' => "This is the all requests.",
            'Attended Subscriptions' => $subscriptionsWithData,
            'Done Subscriptions' => $DonesubscriptionsWithData
        ];

    }
    //***********************************************************************************************************************\\

    public function show_one_request_for_teacher(Request $request)
    {
        $user = Auth::user();
        $user_id = $user->id;
        $subscription_id = $request->query('subscription_id');
        $subscription = Subscription::where('id',$subscription_id)->where('teacher_id', $user_id)->first();

        $subscriptionsWithData = [];

            $subject = Subject::find($subscription->subject_id);
            $user = User::find($subscription->user_id);

            $subscriptionData = [
                'subscription' => $subscription,
                'subject' => $subject,
                'user' => $user
            ];

            $subscriptionsWithData[] = $subscriptionData;
        return [
            'message' => "This is the all requests.",
            'Subscription' => $subscriptionData,
        ];
    }
    //********************************************************************************************************************* */
    public function show_one_request_for_student(Request $request)
    {
        $user = Auth::user();
        $user_id = $user->id;
        $subscription_id = $request->query('subscription_id');
        $subscription = Subscription::where('id',$subscription_id)->where('user_id', $user_id)->first();

        $subscriptionsWithData = [];

            $subject = Subject::find($subscription->subject_id);
            $user = User::find($subscription->teacher_id);

            $subscriptionData = [
                'subscription' => $subscription,
                'subject' => $subject,
                'user' => $user
            ];

            $subscriptionsWithData[] = $subscriptionData;
        return [
            'message' => "This is the all requests.",
            'Subscription' => $subscriptionData,
        ];
    }
    //********************************************************************************************************************* */
    public function edit_request(Request $request)
    {
        $user = Auth::user();
        $user_id = $user->id;

        $request->validate([
            'subscription_id' => 'required',
            'status' => 'required',
        ]);
        $subscription_id = $request->subscription_id;
        $status = $request->status;

        $subscription = Subscription::find($subscription_id);

        $subscription->status = $status;
        $subscription->save();

        return response()->json([
            'message' => 'Request updated successfully',
            'data' => $subscription,
        ]);
    }
    //********************************************************************************************************* */
    public function delete_request(Request $request)
    {
        $user = Auth::user();
        $user_id = $user->id;
        $subscription_id = $request->query('subscription_id');


        $subscription = Subscription::find($subscription_id);

        $subscription->delete();

        return response()->json([
            'message' => 'Request deleted successfully',
            'data' => $subscription,
        ]);
    }
    //********************************************************************************************************* */
}
