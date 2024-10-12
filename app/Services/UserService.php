<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Subject;
use App\Models\User;

use Twilio\Rest\Client;
use Illuminate\Http\Response;
use App\Http\Responses\ApiSuccessResponse;
use App\Http\Responses\ApiErrorResponse;

class  UserService
{
    public function getUsersBySubject($subject)
    {
        return User::whereIn('id', function ($query) use ($subject) {
            $query->select('user_id')
                ->from('teacher_subject_years')
                ->where('subject_id', $subject->id);
        })->get();
    }

    public function attachUsersToSubjects($subjects)
    {
        return $subjects->map(function ($subject) {
            $subjectUsers = $this->getUsersBySubject($subject);
            $subject->users = $subjectUsers;
            return $subject;
        });
    }



    public function search($name) {
        return User::where('name', 'like', '%' . $name . '%')
            ->where('role_id', 3)
            ->where('email', '!=', 'deleted_user@example.com')
            ->get();
    }
}
