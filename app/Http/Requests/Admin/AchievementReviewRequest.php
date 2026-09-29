<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AchievementReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canReviewAchievements() ?? false;
    }

    public function rules(): array
    {
        return [
            'decision' => ['required', 'in:approve,reject'],
            'rejection_feedback' => ['required_if:decision,reject', 'nullable', 'string', 'min:10', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'rejection_feedback.required_if' => 'Rejection requires meaningful reviewer feedback.',
            'rejection_feedback.min' => 'Reviewer feedback must be at least 10 characters.',
        ];
    }
}
