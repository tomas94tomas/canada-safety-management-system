<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReportRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'anonymous' => ['required', 'boolean'],
            'subject' => ['required', 'string'],
            'location' => ['required', 'string'],
            'description' => ['required', 'string'],
            'proposal' => ['required', 'string'],
            'company_name' => ['required', 'string'],
            'catastrophic_high_risk' => ['required', 'boolean'],
            'email' => ['required_if:anonymous,0', 'email'],
            'uploadedFiles' => ['nullable', 'array', 'min:1', 'max:3'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function messages()
    {
        return [
            'email.required_if' => 'The email field is required'
        ];
    }
}
