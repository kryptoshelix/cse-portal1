<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = [
        'documentable_type',
        'documentable_id',
        'disk',
        'path',
        'original_name',
        'mime',
        'size_bytes',
        'uploaded_by',
        'is_private',
    ];

    protected $casts = [
        'is_private' => 'boolean',
    ];

    public function documentable()
    {
        return $this->morphTo();
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
