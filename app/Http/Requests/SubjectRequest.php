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

        if(request()->routeIs('show_all_subjects')){
            return [
                'year_id' => ['required']
            ];
            }elseif(request()->routeIs('search_to_subject')){
                return [
                    'year_id' => ['required']
                ];
                }elseif(request()->routeIs('add_subject')){
                    return [
                        'name' => ['required'],
                        'image_data' => ['required'],
                        'years_content' => ['required'],
                        'years_content.*.year_id' => ['required|integer'],
                    ];
                }elseif(request()->routeIs('edit_subject')){
                    return [
                        'subject_id' => ['required'],
                        'name' => ['required'],
                        'image_data' => ['required'],
                        'years_content' => ['required'],
                        'years_content.*.year_id' => ['required|integer'],
                    ];
                }else{
                    return [
                        ['no validator']
                    ];
                }
            }
        }




