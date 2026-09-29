<?php

namespace App\Models;

use App\Enums\AchievementStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Achievement extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * SECURITY: status, reviewed_by, reviewed_at, approved_at, published_at,
     * featured, submitted_at and owner/created_by ids are NOT fillable here —
     * they are set exclusively by controllers/services after authorization.
     */
    protected $fillable = [
        'title',
        'description',
        'achievement_category_id',
        'achievement_date',
        'issuing_organization',
        'level',
        'public_display_consent',
    ];

    protected $casts = [
        'achievement_date' => 'date',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'approved_at' => 'datetime',
        'published_at' => 'datetime',
        'status' => AchievementStatus::class,
        'featured' => 'boolean',
        'public_display_consent' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(AchievementCategory::class, 'achievement_category_id');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function participants(): HasMany
    {
        return $this->hasMany(AchievementPerson::class);
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    /* ---------- Scopes ---------- */

    /** Public visibility: only published rows. */
    public function scopePublished(Builder $q): Builder
    {
        return $q->where('status', AchievementStatus::Published->value);
    }

    public function scopeStatus(Builder $q, ?string $status): Builder
    {
        return $q->when($status && in_array($status, AchievementStatus::values(), true),
            fn (Builder $qq) => $qq->where('status', $status));
    }
}
