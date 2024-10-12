<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Subject;
use App\Models\User;

use Twilio\Rest\Client;
use Illuminate\Http\Response;
use App\Http\Responses\ApiSuccessResponse;
use App\Http\Responses\ApiErrorResponse;

class  SubjectService
{
    //******************************************************************************************* */

    protected $userService;


    public function __construct(
        UserService $userService
    ) {
        $this->userService = $userService;
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

        return Subject::where('category_id', $category_id)
            ->where('exist', true)
            ->get();
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

}
