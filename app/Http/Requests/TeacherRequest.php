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

        if(request()->routeIs('search_to_teacher')){
            return [
                'name' => ['required'],
                'year_id' => ['required']
            ];
            }elseif(request()->routeIs('add_teacher')){
                return [
                    'name' => ['required'],
                    // 'image_data' => ['required'],
                    'description' => ['required'],
                    'year_id' => ['required']
                ];
                }elseif(request()->routeIs('edit_teacher')){
                    return [
                        'teacher_id' => ['required'],
                        'name' => ['required'],
                    // 'image_data' => ['required'],
                    'description' => ['required'],
                    'content' => ['required|array'],
                    'content.*.year_id' => 'required|integer',
                    'content.*.subject_id' => 'required|integer'
                    ];
                }else{
                    return [
                        ['no validator']
                    ];
                }
            }
        }
