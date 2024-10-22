<?php

namespace App\Services;

use App\Models\Lesson;

use App\Models\TeacherSubjectYear;

class  LessonService
{

    protected $userService;
    protected $videoService;
    protected $fileService;

    public function __construct(
        UserService $userService,

        VideoService $videoService,
        FileService $fileService

    ) {
        $this->userService = $userService;
        $this->videoService = $videoService;
        $this->fileService = $fileService;
    }
    //*******************************************************************************************
    public
    function getLessons($unit_id)
    {

        return
        Lesson::where('unit_id', $unit_id)
        ->where('exist',
            true
        )
            ->get();
    }
    //*******************************************************************************************
    public
    function getLesson($lesson_id)
    {
        return Lesson::where('id', $lesson_id)->first();
    }
    //******************************************************************************************* */

    public function search($name)
    {
        return Lesson::where('name', 'like', '%' . $name . '%')
            ->where('exist', true)
            ->get();
    }
//******************************************************************************************* */
public function accessLesson($user_id, $subject_id)
{
    return TeacherSubjectYear::where('user_id', $user_id)
        ->where('subject_id', $subject_id)
        ->exists();
}
//*******************************************************************************************

    public function deleteLessons($subject_id)
    {
        Lesson::whereIn('unit_id', function ($query) use ($subject_id) {
            $query->select('id')
                ->from('units')
                ->where('subject_id', $subject_id);
        })->update(['exist' => false]);
    }
}
