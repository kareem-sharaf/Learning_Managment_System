<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
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
    public function rules()
    {
        $categoryId = $this->route('category') ?? $this->route('category_id');

        return [
            'category' => [
                'required',
                'string',
                Rule::unique('categories')->ignore($categoryId),
            ],
            'image' => 'required|image',
        ];
    }
}
