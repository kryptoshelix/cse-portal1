<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;

class AchievementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // policy handles record-level access in controller
    }

    protected function prepareForValidation(): void
    {
        // SECURITY: privileged fields are stripped — they can never be set by portal users.
        $this->request->remove('status', 'approved_by', 'reviewed_by', 'created_by',
            'owner_user_id', 'featured', 'published_at', 'approved_at', 'is_admin');
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:5', 'max:200'],
            'description' => ['nullable', 'string', 'max:5000'],
            'achievement_category_id' => ['required', 'integer', 'exists:achievement_categories,id'],
            'achievement_date' => ['required', 'date', 'before_or_equal:today'],
            'issuing_organization' => ['nullable', 'string', 'max:200'],
            'level' => ['required', 'in:Institutional,Regional,National,International'],
            'public_display_consent' => ['nullable', 'boolean'],
            'evidence' => ['nullable', 'file', 'max:5120', 'mimes:pdf,doc,docx,jpg,jpeg,png'],
        ];
    }

    public function messages()
    {
        return [
            'evidence.mimes' => 'Evidence must be a PDF, DOC, DOCX, JPG or PNG file.',
            'evidence.max' => 'Evidence file must be 5 MB or smaller.',
        ];
    }
}
