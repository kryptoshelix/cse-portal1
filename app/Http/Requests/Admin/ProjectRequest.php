<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ProjectRequest extends FormRequest
{
    use AdminOnly;

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string', 'max:5000'],
            'category' => ['nullable', 'string', 'max:100'],
            'tech_stack' => ['nullable', 'string', 'max:255'],
            'year' => ['nullable', 'integer', 'between:1950,2100'],
            'guide_faculty_id' => ['nullable', 'integer', 'exists:faculty,id'],
            'members' => ['nullable', 'string', 'max:255'],
            'featured' => ['nullable', 'boolean'],
        ];
    }
}
