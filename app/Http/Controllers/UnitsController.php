<?php

namespace App\Http\Controllers;

use App\Models\Subject;

use App\Models\Lesson;
use App\Models\Unit;
use App\Models\Video;
use App\Models\TeacherSubjectYear;

use App\Services\UserService;
use App\Services\UnitService;
use App\Services\LessonService;
use App\Services\VideoService;
use App\Services\FileService;
use App\Services\SubjectService;
use App\Services\ImageService;

use App\Http\Requests\UnitRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\Auth;

class UnitsController extends Controller
{
    protected $userService;
    protected $unitService;
    protected $lessonService;
    protected $videoService;
    protected $fileService;
    protected $subjectService;
    protected $imageService;

    public function __construct(
        UserService $userService,
        UnitService $unitService,
        LessonService $lessonService,
        VideoService $videoService,
        FileService $fileService,
        SubjectService $subjectService,
        ImageService $imageService,

    ) {
        $this->userService = $userService;
        $this->unitService = $unitService;
        $this->lessonService = $lessonService;
        $this->videoService = $videoService;
        $this->fileService = $fileService;
        $this->subjectService = $subjectService;
        $this->imageService = $imageService;
    }
    //******************************************************************************************* */
    public function show_all_units($subject_id)
    {
        $subject = $this->subjectService->getSubject($subject_id);
        if (!$subject) {
            return response()->json(['error' => 'Subject does not exist!.'], 404);
        }

        $units = $this->unitService->getUnits($subject_id);
        if ($units->isEmpty()) {
            return response()->json(['message' => 'No units found in this subject!'], 404);
        }

        return response()->json([
            'message' => 'This is all units',
            'data' => $units
        ]);
    }
    //*******************************************************************************************
    public function show_unit($unit_id)
    {


        $unit = $this->unitService->getUnit($unit_id);
        if (!$unit) {
            return response()->json(['message' => 'unit does not exist!'], 404);
        }

        return response()->json([
            'message' => 'This is the unit',
            'data' => $unit
        ]);
    }
    //************************************************************************************************************** */
    public
    function add_unit(UnitRequest $request)
    {
        $user_id = Auth::id();
        if (!$this->unitService->accessUnit($user_id, $request->subject_id)) {
            return response()->json(['message' => 'You cannot add this unit.'], 403);
        }

        $data = $request->validated();

        $data['image'] = $this->imageService->uploadImage($request->file('image'), 'units_images');
        $unit = Unit::create($data);


        // Handle video upload
        if ($request->hasFile('video')) {
            $this->videoService->saveVideo($request->file('video'), $unit, $request->video_name);
        }

        // Handle file upload
        if ($request->hasFile('file')) {
            $this->fileService->saveFile($request->file('file'), $unit, $request->file_name);
        }

        return response()->json([
            'message' => 'Unit added successfully',
            'data' => $unit,
        ]);
    }

    //**************************************************************** */
    public function edit_unit(UnitRequest $request, $unit_id)
    {
        $user_id = Auth::id();
        if (!$this->unitService->accessUnit($user_id, $request->subject_id)) {
            return response()->json(['message' => 'You cannot edit this unit.'], 403);
        }
        $data = $request->validated();
        $unit = $this->unitService->getUnit($unit_id);

        if (!$unit) {
            return response()->json(
                [
                    'message' => 'unit does not exist!.',
                ],
                404
            );
        }


        // Handle image upload
        if ($request->hasFile('image')) {
            $data['image'] = $this->imageService->replaceImage($request->file('image'), $unit->image, 'units_images');
        }
        // Handle video upload
        if ($request->hasFile('video')) {
            $this->videoService->replaceVideo($request->file('video'), $unit, $request->video_name);
        }
        // Handle file upload
        if ($request->hasFile('file')) {
            $this->fileService->replaceFile($request->file('file'), $unit, $request->file_name);
        }
        // Update subject data
        $unit->update($data);
        return response()->json(
            [
                'message' => 'unit updated successfully',
                'data' => $unit,
            ],
            200
        );
    }


    //********************************************************************************************************************************************* */
    public function delete_unit($unit_id)
    {
        $user_id = Auth::id();

        $unit = Unit::find($unit_id);
        if (!$this->unitService->accessUnit($user_id, $unit->subject_id)) {
            return response()->json(['message' => 'You cannot delete this unit.'], 403);
        }

        $this->unitService->deleteUnitWithRelations($unit_id,$unit->subject_id);
        return response()->json(['message' => 'Unit and related items have been deleted successfuly.']);

    }
}
//******************************************************************************************************************************************* */
