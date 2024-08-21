<?php

namespace App\Http\Requests;

use App\Models\StudentCard;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class sudentCardCreateRequest extends FormRequest
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
        $studentId = $this->input('student_id');

        $rules = [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                // Unique validation ignoring current student_id if it's not null
                Rule::unique('student_card', 'email')->ignore($studentId, 'student_id'),
            ],
            'address' => 'required|string',
            'student_id' => 'required|integer',
            'phone_no' => [
                'required',
                'string',
                'regex:/^\+?\d{10,}$/',
                function ($attribute, $value, $fail) use ($studentId) {
                    // Normalize the phone number
                    $normalizedPhone = preg_replace('/[^\d]+/', '', $value);

                    // Check if the normalized phone number is unique, ignoring current student_id if it's not null
                    $query = StudentCard::where('phone_no', $normalizedPhone);
                    if (!is_null($studentId)) {
                        $query->where('student_id', '!=', $studentId);
                    }
                    $exists = $query->exists();

                    if ($exists) {
                        $fail('The '.$attribute.' has already been taken.');
                    }
                }
            ],
        ];

        return $rules;
    }
}
