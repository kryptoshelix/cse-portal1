<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ActivityRequest extends FormRequest
{
    use AdminOnly;

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string', 'max:5000'],
            'type' => ['required', 'in:event,workshop,seminar,guest_lecture,social'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'venue' => ['nullable', 'string', 'max:200'],
            'organizer' => ['nullable', 'string', 'max:200'],
            'featured' => ['nullable', 'boolean'],
        ];
    }
}
