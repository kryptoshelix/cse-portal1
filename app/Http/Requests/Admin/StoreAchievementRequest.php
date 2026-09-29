<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreAchievementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    protected function prepareForValidation(): void
    {
        // Admins still cannot inject review timestamps; only whitelisted content fields are used.
        $this->request->remove('reviewed_by', 'reviewed_at', 'approved_at', 'published_at');
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
            'owner_user_id' => ['required', 'integer', 'exists:users,id'],
            'featured' => ['nullable', 'boolean'],
            'public_display_consent' => ['nullable', 'boolean'],
            'evidence' => ['nullable', 'file', 'max:5120', 'mimes:pdf,doc,docx,jpg,jpeg,png'],
        ];
    }
}
