<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class News extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['heading', 'slug', 'body', 'excerpt', 'pinned', 'author_user_id'];

    protected $casts = ['published_at' => 'datetime', 'is_published' => 'boolean', 'pinned' => 'boolean'];

    public static function boot(): void
    {
        parent::boot();
        static::creating(function ($n) {
            if (empty($n->slug)) {
                $base = Str::slug($n->heading);
                $slug = $base; $i = 1;
                while (static::withTrashed()->where('slug', $slug)->exists()) { $slug = $base.'-'.$i++; }
                $n->slug = $slug;
            }
        });
    }

    public function scopePublished(Builder $q): Builder
    {
        return $q->where('is_published', true)->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_user_id');
    }
}
