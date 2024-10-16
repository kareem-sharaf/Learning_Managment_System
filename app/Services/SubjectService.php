<?php

namespace App\Services;

use App\Models\Subject;
use App\Models\Year;


class

SubjectService
{


    //******************************************************************************************* */

    protected $userService;
    protected $unitService;
    protected $lessonService;
    protected $videoService;
    protected $fileService;

    public function __construct(
        UserService $userService,
        UnitService $unitService,
        LessonService $lessonService,
        VideoService $videoService,
        FileService $fileService

    ) {
        $this->userService = $userService;
        $this->unitService = $unitService;
        $this->lessonService = $lessonService;
        $this->videoService = $videoService;
        $this->fileService = $fileService;
    }
    //******************************************************************************************* */

    public function getSubjects($category_id, $year_id)
    {
        if ($category_id == 1 && $year_id) {
            return Subject::where('category_id', $category_id)
                ->whereHas('years_users', function ($query) use ($year_id) {
                    $query->where('teacher_subject_years.year_id', $year_id);
                })
                ->where('exist', true)
                ->get();
        }

        return
            Subject::where('category_id', $category_id)
            ->where('exist', true)
            ->get();
    }
    //******************************************************************************************* */

    public function getSubject($subject_id)
    {
        $subject = Subject::where('id', $subject_id)->first();
        if ($subject) {
            return $subject;
        } else {
            return null;
        }
    }
    //******************************************************************************************* */

    public function search($name)
    {
        $subjects = Subject::where('name', 'like', '%' . $name . '%')
            ->where('exist', true)
            ->get();
        return $this->userService->attachUsersToSubjects($subjects);
    }
    //******************************************************************************************* */
    public function associateYear($subject, $yearId, $userId)
    {
        $existingYear = Year::find($yearId);
        if (! $existingYear) {
            throw new \Exception('Year not found.');
        }

        $subject->years_users()->attach($userId, ['year_id' => $yearId]);
    }

    //******************************************************************************************* */

    public function associateUser($subject, $userId)
    {
        $subject->years_users()->attach($userId);
    }
    //******************************************************************************************* */
    public function deleteSubjectWithRelations($subject_id)
    {
        $subject = $this->getSubject($subject_id);

        if ($subject) {
            $this->unitService->deleteUnits($subject_id);
            $this->lessonService->deleteLessons($subject_id);
            $this->videoService->deleteVideos($subject_id);
            $this->fileService->deleteFiles($subject_id);
            $this->deleteSubject($subject);
        }
    }

    //*******************************************************************************************
    public function deleteSubject(Subject $subject)
    {
        $subject->update(['exist' => false]);
    }
}
