<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Activity extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title', 'slug', 'description', 'type', 'start_date', 'end_date',
        'venue', 'organizer',
    ];

    protected $casts = ['start_date' => 'date', 'end_date' => 'date', 'is_published' => 'boolean', 'featured' => 'boolean'];

    public static function boot(): void
    {
        parent::boot();
        static::creating(function ($a) {
            if (empty($a->slug)) {
                $base = Str::slug($a->title);
                $slug = $base; $i = 1;
                while (static::withTrashed()->where('slug', $slug)->exists()) { $slug = $base.'-'.$i++; }
                $a->slug = $slug;
            }
        });
    }

    public function scopePublished(Builder $q): Builder
    {
        return $q->where('is_published', true);
    }
}
