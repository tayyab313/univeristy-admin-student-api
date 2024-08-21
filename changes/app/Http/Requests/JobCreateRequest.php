<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JobCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        return true; // Change to your authorization logic
    }

    public function rules()
    {
        return [
            'title'         => 'required|string|max:255',
            'company'       => 'required|string|max:255',
            'location'      => 'required|string|max:255',
            'description'   => 'required|string',
            'contact_email' => 'required|email|max:255',
            'created_by'    => 'required|integer',
        ];
    }
}
