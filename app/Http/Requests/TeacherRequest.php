<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TeacherRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $name = '';
        $year_id = '';
        $image_data='';
        $description='';
        $content = '';
        $teacher_id = '';


        if(request()->routeIs('search_to_teacher')){
            $name='required';
            $year_id='required';
        }elseif(request()->routeIs('add_teacher')){
            $name='required';
            //$image_data='required';
            $description='required';
            $year_id='required';
        }elseif(request()->routeIs('add_subject')){
            $name='required';
            //$image_data='required';
            $content='required|array';
            $description='required';
        }elseif(request()->routeIs('edit_teacher')){
            $teacher_id='required';
            $name='required';
            //$image_data='required';
            $description='required';
            $content='required|array';
        }

        return [
            'teacher_id' => $teacher_id,
            'year_id' => $year_id,
            'name' => $name,
            'description' => $description,
           // 'image_data' => $image_data,
            'content' => $years_content,
            'content.*.year_id' => 'required|integer',
            'content.*.subject_id' => 'required|integer'

        ];
    }
}

