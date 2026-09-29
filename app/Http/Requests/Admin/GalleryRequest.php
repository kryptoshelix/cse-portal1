<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class GalleryRequest extends FormRequest
{
    use AdminOnly;

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:200'],
            'caption' => ['nullable', 'string', 'max:500'],
            'taken_on' => ['nullable', 'date'],
            'image' => [$this->route('galleryItem') ? 'nullable' : 'required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ];
    }
}
