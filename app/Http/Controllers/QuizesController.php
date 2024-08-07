<?php

namespace App\Http\Controllers;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Year;
use App\Models\Stage;
use App\Models\SubjectYear;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\Subscription;
use App\Models\TeacherSubjectYear;
use App\Models\Unit;
use App\Models\Lesson;
use App\Models\StudentExam;

use App\Http\Responses\ApiSuccessResponse;
use App\Http\Responses\ApiErrorResponse;

use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\TeachersController;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;

class QuizesController extends Controller
{
    //show all quizzes to teacher whether it's public or not.
    public function show_all_to_teacher(Request $request)
{
    $user_id = Auth::id();

    $validated = $request->validate([
        'type_id' => 'required|integer',
        'type' => 'required|string|in:subject,unit,lesson'
    ]);

    $typeMapping = [
        'subject' => 'App\Models\Subject',
        'unit' => 'App\Models\Unit',
        'lesson' => 'App\Models\Lesson',
    ];

    $typeType = $typeMapping[$validated['type']] ?? null;

    if (!$typeType) {
        return response()->json(['error' => 'Invalid type'], 400);
    }

    $quizzes = Quiz::where('type_id', $validated['type_id'])
                ->where('type_type', $typeType)
                ->get();

    $response = [];
    foreach ($quizzes as $quiz) {
        $quiz->load('questions');

        foreach ($quiz->questions as $question) {
            $question->answers = json_decode($question->answers);
        }

        $response[] = [
            'quiz' => $quiz,
            'questions' => $quiz->questions
        ];
    }

    return response()->json($response);
}

    /************************************************************************ */
    public function add_quiz(Request $request)
{
    $user_id = Auth::id();

    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'duration' => 'required|integer',
        'success_mark' => 'required|integer',
        'public' => 'required|boolean',
        'type_type' => 'required|string|in:subject,unit,lesson',
        'type_id' => 'required|integer',
        'questions' => 'required|array',
        'questions.*.text' => 'required|string',
        'questions.*.mark' => 'required|integer',
        'questions.*.answers' => 'required|array',
        'questions.*.correct_answer' => 'required|string',
    ]);

    $typeMapping = [
        'subject' => 'App\Models\Subject',
        'unit' => 'App\Models\Unit',
        'lesson' => 'App\Models\Lesson',
    ];

    $type = $validated['type_type'];
    $typeClass = $typeMapping[$type];

    $totalMark = 0;
    $isTeacher = false;

    if ($typeClass == 'App\Models\Subject') {
        $subject = Subject::find($validated['type_id']);
        if (!$subject) {
            return response()->json(['message' => 'Subject does not exist.'], 201);
        }
        $TeacherSubject = TeacherSubjectYear::where('user_id', $user_id)
                                            ->where('subject_id', $validated['type_id'])
                                            ->first();
        if ($TeacherSubject) {
            $isTeacher = true;
        }
    } elseif ($typeClass == 'App\Models\Unit') {
        $unit = Unit::find($validated['type_id']);
        if (!$unit) {
            return response()->json(['message' => 'Unit does not exist.'], 201);
        }
        $subject_id = $unit->subject_id;
        $TeacherSubject = TeacherSubjectYear::where('user_id', $user_id)
                                            ->where('subject_id', $subject_id)
                                            ->first();
        if ($TeacherSubject) {
            $isTeacher = true;
        }
    } elseif ($typeClass == 'App\Models\Lesson') {
        $lesson = Lesson::find($validated['type_id']);
        if (!$lesson) {
            return response()->json(['message' => 'Lesson does not exist.'], 201);
        }
        $unit_id = $lesson->unit_id;
        $unit = Unit::find($unit_id);
        $subject_id = $unit->subject_id;
        $TeacherSubject = TeacherSubjectYear::where('user_id', $user_id)
                                            ->where('subject_id', $subject_id)
                                            ->first();
        if ($TeacherSubject) {
            $isTeacher = true;
        }
    }

    if ($isTeacher) {
        foreach ($validated['questions'] as $questionData) {
            $totalMark += $questionData['mark'];
        }

        $quiz = new Quiz();
        $quiz->name = $validated['name'];
        $quiz->duration = $validated['duration'];
        $quiz->total_mark = $totalMark;
        $quiz->success_mark = $validated['success_mark'];
        $quiz->public = $validated['public'];
        $quiz->type_id = $validated['type_id'];
        $quiz->type_type = $typeClass;
        $quiz->teacher_id = $user_id;
        $quiz->save();

        foreach ($validated['questions'] as $questionData) {
            $question = new Question();
            $question->text = $questionData['text'];
            $question->mark = $questionData['mark'];
            $question->answers = json_encode($questionData['answers']); // يتم تحويل الإجابات إلى JSON
            $question->correct_answer = $questionData['correct_answer'];
            $question->quiz_id = $quiz->id;
            $question->save();
        }

        return response()->json(['message' => 'Quiz created successfully', 'quiz' => $quiz], 201);
    } else {
        return response()->json(['message' => 'You cannot create quiz here.'], 201);
    }
}
    /************************************************************************ */
    public function edit_quiz(Request $request)
    {
            $user_id = Auth::id();

            $validated = $request->validate([
                'quiz_id' => 'required|integer',
                'name' => 'required|string|max:255',
                'duration' => 'required|integer',
                'success_mark' => 'required|integer',
                'public' => 'required|boolean',
                'type_id' => 'required|integer',
                'type_type' => 'required|string',
                'questions' => 'array',
                'questions.*.id' => 'sometimes|integer|exists:questions,id',
                'questions.*.text' => 'string',
                'questions.*.mark' => 'integer',
                'questions.*.answers' => 'array',
                'questions.*.correct_answer' => 'string',
            ]);

            $quiz = Quiz::where('id', $validated['quiz_id'])->where('teacher_id', $user_id)->first();

            if (!$quiz) {
                return response()->json(['error' => 'Quiz not found or you are not the teacher'], 404);
            }
            if($quiz->teacher_id == $user_id){
                $typeMapping = [
                    'subject' => 'App\Models\Subject',
                    'unit' => 'App\Models\Unit',
                    'lesson' => 'App\Models\Lesson',
                ];

                $type = $validated['type_type'];
                $typeClass = $typeMapping[$type];

            $quiz->name = $validated['name'];
            $quiz->duration = $validated['duration'];
            $quiz->success_mark = $validated['success_mark'];
            $quiz->public = $validated['public'];
            $quiz->type_id = $validated['type_id'];
            $quiz->type_type = $validated['type_type'];

            $totalMark = 0;
            foreach ($validated['questions'] as $questionData) {
                $totalMark += $questionData['mark'];
            }
            $quiz->total_mark = $totalMark;

            $quiz->save();

            $quiz->questions()->delete();

            foreach ($validated['questions'] as $questionData) {
                $question = new Question();
                $question->text = $questionData['text'];
                $question->mark = $questionData['mark'];
                $question->answers = json_encode($questionData['answers']);
                $question->correct_answer = $questionData['correct_answer'];
                $question->quiz_id = $quiz->id;
                $question->save();
            }

            return response()->json(['success' => 'Quiz and questions updated successfully'], 200);
            }else{
                return response()->json([
                    'message' => 'You cannot edite quiz here.'], 201);
            }
    }
    /************************************************************************ */
    public function delete_quiz(Request $request)
    {
        $user_id = Auth::id();

        $quiz = Quiz::find($request->quiz_id);
        if (!$quiz) {
            return response()->json([
                'message' => "The quiz doesn't exist."], 201);

        }
        if($quiz->teacher_id == $user_id){
        $quiz->questions()->delete();
        $quiz->delete();
        return response()->json([
            'message' => 'The quiz deleted successfully.'], 201);
        }else{
            return response()->json([
                'message' => 'You cannot delete the quiz here.'], 201);
        }
    }
    /************************************************************************ */
    public function show_all_to_student(Request $request)
{
    $user_id = Auth::id();

    $validated = $request->validate([
        'type_id' => 'required|integer',
        'type' => 'required|string',
        'subject_id' => 'required|integer'
    ]);

    $typeMapping = [
        'subject' => 'App\Models\Subject',
        'unit' => 'App\Models\Unit',
        'lesson' => 'App\Models\Lesson',
    ];

    $typeType = $typeMapping[$validated['type']] ?? null;

    if (!$typeType) {
        return response()->json(['error' => 'Invalid type'], 400);
    }

    $openQuizzes = Quiz::where('type_id', $validated['type_id'])
                    ->where('type_type', $typeType)
                    ->where('public', true)
                    ->get();

    $lockQuizzes = Quiz::where('type_id', $validated['type_id'])
                    ->where('type_type', $typeType)
                    ->where('public', false)
                    ->get();

    // Load questions for each quiz and decode answers
    $openQuizzes->load('questions');
    foreach ($openQuizzes as $quiz) {
        foreach ($quiz->questions as $question) {
            $question->answers = json_decode($question->answers);
        }
    }

    $subscription = Subscription::where('user_id', $user_id)
                                ->where('subject_id', $validated['subject_id'])
                                ->first();

    if ($subscription) {
        $lockQuizzes->load('questions');
        foreach ($lockQuizzes as $quiz) {
            foreach ($quiz->questions as $question) {
                $question->answers = json_decode($question->answers);
            }
        }

        return response()->json([
            'OpenQuizzes' => $openQuizzes,
            'LockQuizzes' => $lockQuizzes->map(function ($quiz) {
                return [
                    'quiz' => $quiz,
                ];
            }),
        ]);
    } else {
        return response()->json([
            'OpenQuizzes' => $openQuizzes,
            'LockQuizzes' => $lockQuizzes,
        ]);
    }
}

    /************************************************************************ */
    public function take_quiz(Request $request)
    {
        $user_id = Auth::id();
        $validated = $request->validate([
            'quiz_id' => 'required|integer|exists:quizzes,id',
            'answers' => 'required|array',
            'answers.*' => 'required|string',
        ]);
        $old_student_exams = StudentExam::where('quiz_id',$validated['quiz_id'])
                                        ->where('user_id',$user_id)
                                        ->first();
        if($old_student_exams){
            return response()->json([
                'message' => "you cannot take this exam because you've done it before."
            ]);
        }else{
        $quiz = Quiz::with('questions')->find($request->quiz_id);
        if (!$quiz) {
            return response()->json(['error' => 'Quiz not found'], 404);
        }


        $typeClass = $quiz->type_type ;





        if ($typeClass == 'App\Models\Subject') {
            $subject_id = $quiz->type_id;
        } elseif ($typeClass == 'App\Models\Unit') {
            $unit = Unit::find($quiz->type_id);
            $subject_id = $unit->subject_id;
        } elseif ($typeClass == 'App\Models\Lesson') {
            $lesson = Lesson::find($quiz->type_id);
            $unit_id = $lesson->unit_id;
            $unit = Unit::find($unit_id);
            $subject_id = $unit->subject_id;
        }

        if($quiz->public == 1){
        $studentSubscription = Subscription::where('user_id',$user_id)
                                            ->where('subject_id',$subject_id)
                                            ->first();
        if(!$studentSubscription){
            return response()->json([
                'message' => "you cannot take this exam because you don't subscription the subject."
            ]);
        }
        }
        $totalScore = 0;

        foreach ($quiz->questions as $question) {
            if (isset($validated['answers'][$question->id]) && $validated['answers'][$question->id] == $question->correct_answer) {
                $totalScore += $question->mark;
            }
        }

        $passed = $totalScore >= $quiz->success_mark;

        $student_exams = new StudentExam();
        $student_exams->user_id = $user_id;
        $student_exams->quiz_id = $validated['quiz_id'];
        $student_exams->mark = $totalScore;
        $student_exams->status = $passed;
        $student_exams->date = now();
        $student_exams->save();

        return response()->json([
            'message' => 'you take the quiz successfuly',
            'score' => $totalScore,
            'passed' => $passed,
        ]);
        }
    }
    /************************************************************************ */
}
