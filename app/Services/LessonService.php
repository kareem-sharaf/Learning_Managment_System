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
}
