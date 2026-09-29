<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class NewsRequest extends FormRequest
{
    use AdminOnly;

    public function rules(): array
    {
        return [
            'heading' => ['required', 'string', 'max:200'],
            'body' => ['nullable', 'string', 'max:20000'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'pinned' => ['nullable', 'boolean'],
        ];
    }
}
