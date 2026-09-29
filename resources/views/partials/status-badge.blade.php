@php
    $badge = match ($item->status?->value ?? $item->status ?? null) {
        'draft' => 'secondary', 'pending' => 'warning text-dark', 'rejected' => 'danger',
        'approved' => 'info text-dark', 'published' => 'success', default => 'secondary',
    };
@endphp
<span class="badge bg-{{ $badge }} text-uppercase">{{ $item->status instanceof \App\Enums\AchievementStatus ? $item->status->value : $item->status }}</span>
