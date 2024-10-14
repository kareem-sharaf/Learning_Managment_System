<?php

namespace App\Services;

use App\Models\Lesson;


class  LessonService
{



    public function search($name)
    {
        return Lesson::where('name', 'like', '%' . $name . '%')
            ->where('exist', true)
            ->get();
    }


    public function deleteLessons($subject_id)
    {
        Lesson::whereIn('unit_id', function ($query) use ($subject_id) {
            $query->select('id')
                ->from('units')
                ->where('subject_id', $subject_id);
        })->update(['exist' => false]);
    }
}
