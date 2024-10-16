<?php

namespace App\Services;

use App\Models\Unit;


class  UnitService

{

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
        $this->
        fileService = $fileService;
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
            ->getall();
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

    public function deleteUnits($subject_id)
    {
        Unit::where('subject_id', $subject_id)
            ->update(['exist' => false]);
    }
}
