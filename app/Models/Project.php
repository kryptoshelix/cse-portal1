<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['title', 'description', 'category', 'tech_stack', 'year', 'guide_faculty_id', 'members'];

    protected $casts = ['is_published' => 'boolean', 'featured' => 'boolean'];

    public function guide()
    {
        return $this->belongsTo(Faculty::class, 'guide_faculty_id');
    }

    public function scopePublished(Builder $q): Builder
    {
        return $q->where('is_published', true);
    }
}
