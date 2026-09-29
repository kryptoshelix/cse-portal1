<?php

namespace App\Http\Requests\Auth;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // guests only (route middleware)
    }

    protected function prepareForValidation(): void
    {
        // Strip privileged fields from the request entirely — never trusted.
        $this->request->remove('role', 'status', 'can_review_achievements', 'approved_by', 'is_admin');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email:rfc,dns', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'account_type' => ['required', 'in:'.implode(',', UserRole::registrable())],
            'roll_number' => ['required_if:account_type,student', 'nullable', 'string', 'max:50', 'unique:students,roll_number'],
            'employee_id' => ['required_if:account_type,faculty', 'nullable', 'string', 'max:50', 'unique:faculty,employee_id'],
            'program' => ['nullable', 'string', 'max:100'],
            'batch' => ['nullable', 'integer', 'between:1950,2100'],
            'designation' => ['nullable', 'string', 'max:100'],
            'specialization' => ['nullable', 'string', 'max:150'],
        ];
    }

    /** Only ever returns student|faculty; admins cannot be self-registered. */
    public function registrableRole(): string
    {
        return in_array($this->input('account_type'), UserRole::registrable(), true)
            ? $this->input('account_type')
            : UserRole::Student->value;
    }
}
