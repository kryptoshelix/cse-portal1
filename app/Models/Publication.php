<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Publication extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['title', 'authors', 'journal_or_conference', 'volume_issue', 'publication_date', 'doi', 'url', 'faculty_id', 'abstract'];

    protected $casts = ['publication_date' => 'date', 'is_published' => 'boolean'];

    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }

    public function scopePublished(Builder $q): Builder
    {
        return $q->where('is_published', true);
    }
}
