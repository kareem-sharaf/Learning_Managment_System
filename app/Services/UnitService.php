<?php

namespace App\Services;

use App\Models\Unit;


class  UnitService
{



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
