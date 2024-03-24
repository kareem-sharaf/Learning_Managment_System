<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UnitRequest extends FormRequest
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
        if(request()->routeIs('show_all_units')){
            return [
                'subject_id' => ['required']
            ];
            }elseif(request()->routeIs('search_to_unit')){
                return [
                    'name' => ['required'],
                    'subject_id' => ['required']
                ];
                }elseif(request()->routeIs('add_unit')){
                    return [
                        'name' => ['required'],
                        // 'image' => ['required'],
                    // 'video' => ['required'],
                    'subject_id' => ['required'],
                    'description' => ['required'],
                    ];
                }elseif(request()->routeIs('edit_unit')){
                    return [
                        'unit_id' => ['required'],
                        'name' => ['required'],
                        // 'image' => ['required'],
                    // 'video' => ['required'],
                    'description' => ['required'],
                    ];
                }else{
                    return [
                        ['no validator']
                    ];
                }
            }
        }
