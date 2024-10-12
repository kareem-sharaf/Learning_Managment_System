<?php

namespace App\Services;
use App\Models\Category;
use App\Models\Subject;
use App\Models\User;
use App\Models\Year;

use Twilio\Rest\Client;
use Illuminate\Http\Response;
use App\Http\Responses\ApiSuccessResponse;
use App\Http\Responses\ApiErrorResponse;

 class  YearService
{





    public function index()
    {
        $years = Year::all();
        return $years;
    }



}
