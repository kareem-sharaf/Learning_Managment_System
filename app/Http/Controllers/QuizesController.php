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


use App\Http\Responses\ApiSuccessResponse;
use App\Http\Responses\ApiErrorResponse;

use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\TeachersController;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;

class QuizesController extends Controller
{
    //show all quizzes to teacher whether it's public or not.
    public function show_all(Request $request)
    {
        $request->validate([
            'type_id' => 'required|integer',
            'type' => 'required|string'
        ]);

        $typeMapping = [
            'subject' => 'App\Models\Subject',
            'unit' => 'App\Models\Unit',
            'lesson' => 'App\Models\Lesson',
        ];

        $typeType = $typeMapping[$request->type] ?? null;

        if (!$typeType) {
            return response()->json(['error' => 'Invalid type'], 400);
        }

        $quizzes = Quiz::where('type_id', $request->type_id)
                       ->where('type_type', $typeType)
                       ->get();

        return response()->json($quizzes);
    }
    /************************************************************************ */
    public function show_one_to_teacher(Request $request)
    {
        $user_id = Auth::id();
        $quiz_id = $request->query('quiz_id');


        $quiz = Quiz::where('id', $quiz_id)->first();
        $questions = Question::where('quiz_id', $quiz_id)->get();
        if ($quiz && $quiz->teacher_id == $user_id) {
            return response()->json([
                'quiz' => $quiz,
                'questions' => $quiz->questions
            ]);
        } else {
            return response()->json(['error' => 'Quiz not found or you are not the teacher'], 404);
        }

    }
        /************************************************************************ */

    public function add_quiz(Request $request)
    {
    $user = Auth::user();

    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'duration' => 'required|integer',
        'total_mark' => 'required|integer',
        'success_mark' => 'required|integer',
        'public' => 'required|boolean',
        'type_id' => 'required|integer',
        'type_type' => 'required|string',
        'questions' => 'required|array',
        'questions.*.text' => 'required|string',
        'questions.*.mark' => 'required|integer',
        'questions.*.answers' => 'required|array',
        'questions.*.correct_answer' => 'required|string',
    ]);

    $quiz = new Quiz();
    $quiz->name = $validated['name'];
    $quiz->duration = $validated['duration'];
    $quiz->total_mark = $validated['total_mark'];
    $quiz->success_mark = $validated['success_mark'];
    $quiz->public = $validated['public'];
    $quiz->type_id = $validated['type_id'];
    $quiz->type_type = $validated['type_type'];
    $quiz->teacher_id = $user->id;
    $quiz->save();

    foreach ($validated['questions'] as $questionData) {
        $question = new Question();
        $question->text = $questionData['text'];
        $question->mark = $questionData['mark'];
        $question->answers = json_encode($questionData['answers']);
        $question->correct_answer = $questionData['correct_answer'];
        $question->quiz_id = $quiz->id;
        $question->save();
    }

    return response()->json(['message' => 'Quiz created successfully', 'quiz' => $quiz], 201);
    }
        /************************************************************************ */

    public function edit_quiz(Request $request)
    {
    $user_id = Auth::id();

    $request->validate([
        'quiz_id' => 'required|integer',
        'name' => 'required|string|max:255',
        'duration' => 'required|integer',
        'total_mark' => 'required|integer',
        'success_mark' => 'required|integer',
        'public' => 'required|boolean',
        'type_id' => 'required|integer',
        'type_type' => 'required|string',
        'questions' => 'required|array',
        'questions.*.id' => 'sometimes|integer|exists:questions,id',
        'questions.*.text' => 'required|string',
        'questions.*.mark' => 'required|integer',
        'questions.*.answers' => 'required|array',
        'questions.*.correct_answer' => 'required|string',
    ]);

    $quiz = Quiz::where('id', $request->quiz_id)->where('teacher_id', $user_id)->first();

    if (!$quiz) {
        return response()->json(['error' => 'Quiz not found or you are not the teacher'], 404);
    }
    $quiz->name = $request->name;
    $quiz->duration = $request->duration;
    $quiz->total_mark = $request->total_mark;
    $quiz->success_mark = $request->success_mark;
    $quiz->public = $request->public;
    $quiz->type_id = $request->type_id;
    $quiz->type_type = $request->type_type;
    $quiz->save();

    $quiz->questions()->delete();

    foreach ($request->questions as $questionData) {
        $question = Question::create([
            'text' => $questionData['text'],
            'mark' => $questionData['mark'],
            'answers' => json_encode($questionData['answers']),
            'correct_answer' => $questionData['correct_answer'],
            'quiz_id' => $quiz->id,
        ]);
    }

    return response()->json(['success' => 'Quiz and questions updated successfully'], 200);
    }
        /************************************************************************ */

    public function delete_quiz($quiz_id)
    {
        $user = auth()->user();
        $quiz = Quiz::find($quiz_id);
        if (!$quiz) {
            $message = "The quiz doesn't exist.";
            return response()->json([
                'message' => $message,
            ]);
        }

        $quiz->questions()->delete();
        $quiz->delete();

        $message = "The quiz deleted successfully.";
        return response()->json([
            'message' => $message,
        ]);

    }
    /************************************************************************ */

public function show_to_all(Request $request)
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

    // Load questions for each quiz
    $openQuizzes->load('questions');


    $subscription = Subscription::where('user_id', $user_id)
                                ->where('subject_id', $validated['subject_id'])
                                ->where('status', 'buy')
                                ->first();

    if ($subscription) {
        $lockQuizzes->load('questions');
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

}
