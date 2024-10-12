<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Subject;
use App\Models\User;


class  CategoryService
{

    public function getCategory($category_id)
    {
        $category = Category::where('id', $category_id)->first();
        if ($category) {
            return $category;
        } else{
            return null;
        }
    }

    public function validateCategoryYear($category_id, $year_id)
    {
        if ($category_id == 1 && !$year_id) {
            return false;
        }
        return true;
    }

    public function getSubjects($category_id, $year_id, $category)
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

    public function attachUsersToSubjects($subjects)
    {
        return $subjects->map(function ($subject) {
            $subjectUsers = User::whereIn('id', function ($query) use ($subject) {
                $query->select('user_id')
                    ->from('teacher_subject_years')
                    ->where('subject_id', $subject->id);
            })->get();

            $subject->users = $subjectUsers;
            return $subject;
        });
    }



    public function search($name)
    {
        return Category::where('category', 'like', '%' . $name . '%')
            ->where('exist', true)
            ->get();
    }

}
