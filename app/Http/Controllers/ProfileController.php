<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Stage;
use App\Models\Year;
use App\Models\TeacherSubjectYear;
use App\Models\SubjectYear;
use App\Models\User;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{

    //********************************************************************************************** */
    public function show_all_teachers()
{
    $teachers = User::where('email', '!=', 'deleted_user@example.com')
        ->where('role_id', 3)
        ->with(['subjects' => function ($query) {
            $query->where('exist', true); // التصفية حسب عمود exist في جدول المواد
        }])
        ->get();

    $message = "This is the list of all teachers.";
    return response()->json([
        'message' => $message,
        'data' => $teachers,
    ]);
}
    //********************************************************************************************** */
    public function teachers_in_category(Request $request)
{
    $category_id = $request->category_id;

    $teachers = User::where('email', '!=', 'deleted_user@example.com')
        ->where('role_id', 3)
        ->whereHas('subjects', function ($query) use ($category_id) {
            $query->where('exist', true)
                  ->where('category_id', $category_id);
        })
        ->with(['subjects' => function ($query) use ($category_id) {
            $query->where('exist', true)
                  ->where('category_id', $category_id);
        }])
        ->get();

    $message = "These are the teachers in category " . $category_id;

    return response()->json([
        'message' => $message,
        'data' => $teachers,
    ]);
}
    //********************************************************************************************** */
    public function show_one_teacher(Request $request)
{
        $user_id = $request->user_id;
        $teachers = User::where('email', '!=', 'deleted_user@example.com')
        ->where('role_id', 3)
        ->where('id',$user_id)
        ->with(['subjects' => function ($query) {
            $query->where('exist', true);
        }])
        ->get();

        $message = "This is the list of all teachers.";
        return response()->json([
            'message' => $message,
            'data' => $teachers,
        ]);
}
    //********************************************************************************************** */
    public function show_one_student(Request $request)
{
    $user_id = $request->user_id;

    // استرجاع بيانات المستخدم مع العلاقات المطلوبة
    $user = User::with(['subscriptions.subject', 'studentExams.quiz'])
                ->where('id', $user_id)
                ->first();

    if ($user && $user->role_id == 4) {
        // جلب جميع المواد التي يشترك بها الطالب
        $subjects = $user->subscriptions->map(function ($subscription) {
            return $subscription->subject->only(['id', 'name', 'description', 'price', 'category_id', 'image_url', 'video_id', 'file_id', 'exist']) + [
                'pivot' => [
                    'user_id' => $subscription->user_id,
                    'subject_id' => $subscription->subject_id
                ]
            ];
        });

        // جلب الكويزات التي قدمها الطالب
        $exams = $user->studentExams->map(function ($exam) {
            return [
                'quiz_id' => $exam->quiz_id,
                'mark' => $exam->mark,
                'status' => $exam->status,
                'date' => $exam->date,
            ];
        });

        return response()->json([
            'message' => 'This is the user.',
            'data' => [
                [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'email_sent_at' => $user->email_sent_at,
                    'device_id' => $user->device_id,
                    'verificationCode' => $user->verificationCode,
                    'image_id' => $user->image_id,
                    'birth_date' => $user->birth_date,
                    'gender' => $user->gender,
                    'address_id' => $user->address_id,
                    'role_id' => $user->role_id,
                    'stage_id' => $user->stage_id,
                    'year_id' => $user->year_id,
                    'points' => $user->points,
                    'fcm' => $user->fcm,
                    'balance' => $user->balance,
                    'exist' => $user->exist,
                    'subjects' => $subjects,
                    'exams' => $exams
                ]
            ]
        ]);
    } else {
        return response()->json([
            'message' => 'User not found or not authorized.',
        ], 404);
    }
}
    //****************************************************************************************************** */

    public function deleteProfile()
    {
        $user_id = Auth::id();
        $user = User::where('id', $user_id)->first();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        switch ($user->role_id) {
            case 4: // Student
                $user->delete();
                return response()->json(['message' => 'Profile deleted successfully'], 200);

            case 3: // Teacher
            $subjects = $user->subjects()->wherePivot('user_id', $user_id)->get();
            foreach ($subjects as $subject) {
                $subject->update(['exist' => false]);
            }

            // Mark the teacher's profile as deleted by updating the email
            $user->update([
                'email' => 'deleted_user@example.com',
            ]);
                return response()->json(['message' => 'Teacher profile marked as deleted'], 200);

            case 1: // Manager
                $otherManagersCount = User::where('role_id', 1)->where('id', '!=', $user_id)->count();
                if ($otherManagersCount > 0) {
                    $user->delete();
                    return response()->json(['message' => 'Manager profile deleted successfully'], 200);
                } else {
                    return response()->json(['message' => 'Cannot delete yourself as the only manager'], 403);
                }

            default:
                return response()->json(['message' => 'You are not allowed to delete this profile'], 403);
        }
    }
}
