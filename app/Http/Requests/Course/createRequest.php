<?php

namespace App\Http\Requests\Course;

use Illuminate\Foundation\Http\FormRequest;

class createRequest extends FormRequest
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
            'code'        => 'required|string|max:255|unique:courses,code',
            'name'        => 'required|string|max:255',
            'department'  => 'required|string|max:255',
            'semester'    => 'required|string|max:255',
            'credit_hour' => 'required|string|max:255',
            'created_by'  => 'required|integer',
        ];
    }
}
