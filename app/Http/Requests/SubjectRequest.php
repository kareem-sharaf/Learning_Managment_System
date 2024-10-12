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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'category_id' => 'required|integer|exists:categories,id',
            'year_id' => 'nullable|integer|exists:years,id',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'description' => 'required|string',
            'image' => 'required|image|max:10240',
            'video' => 'nullable|mimes:mp4,mov,avi,flv|max:204800',
            'video_name' => 'nullable|string|max:255',
            'file_name' => 'nullable|string|max:255',
            'file' => 'nullable|file|max:20480',
        ];
    }
}
