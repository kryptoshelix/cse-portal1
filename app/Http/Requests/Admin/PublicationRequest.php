<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PublicationRequest extends FormRequest
{
    use AdminOnly;

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'authors' => ['required', 'string', 'max:255'],
            'journal_or_conference' => ['nullable', 'string', 'max:255'],
            'volume_issue' => ['nullable', 'string', 'max:100'],
            'publication_date' => ['nullable', 'date'],
            'doi' => ['nullable', 'string', 'max:100'],
            'url' => ['nullable', 'url', 'max:255'],
            'faculty_id' => ['nullable', 'integer', 'exists:faculty,id'],
            'abstract' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
