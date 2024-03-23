<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubjectRequest extends FormRequest
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
        $subject_id = '';
        $year_id = '';
        $name='';
        $image_data='';
        $years_content = '';



        if(request()->routeIs('show_all_subjects')){
            $year_id='required';
        }elseif(request()->routeIs('search_to_subject')){
            $year_id='required';
        }elseif(request()->routeIs('add_subject')){
            $name='required';
            //$image_data='required';
            $years_content='required|array';
        }elseif(request()->routeIs('edit_subject')){
            $subject_id='required';
            $name='required';
            //$image_data='required';
            $years_content='required|array';
        }

        return [
            'subject_id' => $subject_id,
            'year_id' => $year_id,
            'name' => $name,
           // 'image_data' => $image_data,
            'years_content' => $years_content,
            'years_content.*.year_id' => 'required|integer',

        ];
    }
}
