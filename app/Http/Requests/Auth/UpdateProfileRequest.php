<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->request->remove('role', 'status', 'can_review_achievements', 'email', 'roll_number', 'employee_id');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:500'],
            'program' => ['nullable', 'string', 'max:100'],
            'batch' => ['nullable', 'integer', 'between:1950,2100'],
            'public_display_consent' => ['nullable', 'boolean'],
            'designation' => ['nullable', 'string', 'max:100'],
            'specialization' => ['nullable', 'string', 'max:150'],
            'qualification' => ['nullable', 'string', 'max:150'],
            'bio' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
