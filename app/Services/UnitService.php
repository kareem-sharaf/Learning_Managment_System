<?php

namespace App\Services;

use App\Models\Unit;


class  UnitService
{



    public function search($name) {
        return Unit::where('name', 'like', '%' . $name . '%')
        ->where('exist', true)
        ->get();
    }
}
