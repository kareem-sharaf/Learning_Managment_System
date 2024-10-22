<?php

namespace App\Services;

use App\Models\Unit;

use App\Models\TeacherSubjectYear;

class  UnitService

{

    protected $userService;
    protected $lessonService;
    protected $videoService;
    protected $fileService;

    public function __construct(
        UserService $userService,

        LessonService $lessonService,
        VideoService $videoService,
        FileService $fileService

    ) {
        $this->userService = $userService;
        $this->lessonService = $lessonService;
        $this->videoService = $videoService;
        $this->fileService = $fileService;
    }
    //******************************************************************************************* */
    public
    function getUnits($subject_id)
    {

        return
        Unit::where('subject_id', $subject_id)
        ->where('exist',
            true
        )
            ->get();
    }
    //******************************************************************************************* */
    public
    function getUnit($unit_id)
    {
        return Unit::where('id', $unit_id)->first();
    }
    //******************************************************************************************* */

    public function search($name)
    {
        return Unit::where('name', 'like', '%' . $name . '%')
        ->where('exist', true)
        ->get();
    }
    //******************************************************************************************* */
    public function accessUnit($user_id, $subject_id)
    {
        return TeacherSubjectYear::where('user_id', $user_id)
            ->where('subject_id', $subject_id)
            ->exists();
    }
    //*******************************************************************************************
    public function deleteUnitWithRelations($unit_id,$subject_id)
    {
        $unit = $this->getUnit($unit_id);
        if ($unit) {
            $this->videoService->deleteVideos($unit_id);
            $this->fileService->deleteFiles($unit_id);
            $this->deleteUnits($unit_id);
        }
    }

    //*******************************************************************************************
    public function deleteUnits($unit_id)
    {
        Unit::where('id', $unit_id)
            ->update(['exist' => false]);
    }
    //*******************************************************************************************

    public function deleteUnitsBySubject($subject_id)
{
    Unit::where('subject_id', $subject_id)
        ->update(['exist' => false]);

}
}
