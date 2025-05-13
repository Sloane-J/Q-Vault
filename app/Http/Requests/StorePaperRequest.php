<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaperRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Set to `false` if you need authorization checks
    }

    public function rules()
    {
        return [
            'paper_file' => 'required|file|mimes:pdf,doc,docx|max:10240', // 10MB max
            'title' => 'required|string|max:255',
            // Add other fields as needed
        ];
    }

    public function messages()
    {
        return [
            'paper_file.mimes' => 'Only PDF, DOC, or DOCX files are allowed.',
            'paper_file.max' => 'File must be less than 10MB.',
        ];
    }
}