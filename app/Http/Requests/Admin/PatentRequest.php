<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PatentRequest extends FormRequest
{
    use AdminOnly;

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'patent_number' => ['nullable', 'string', 'max:100'],
            'filing_date' => ['nullable', 'date'],
            'filing_status' => ['required', 'in:applied,granted'],
            'inventors' => ['required', 'string', 'max:255'],
            'faculty_id' => ['nullable', 'integer', 'exists:faculty,id'],
        ];
    }
}
