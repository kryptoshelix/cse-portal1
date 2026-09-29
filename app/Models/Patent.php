<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patent extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['title', 'patent_number', 'filing_date', 'filing_status', 'inventors', 'faculty_id'];

    protected $casts = ['filing_date' => 'date', 'is_published' => 'boolean'];

    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }

    public function scopePublished(Builder $q): Builder
    {
        return $q->where('is_published', true);
    }
}
