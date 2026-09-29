<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AchievementPerson extends Model
{
    protected $table = 'achievement_people';

    protected $fillable = ['achievement_id', 'user_id', 'participant_name', 'role_in_achievement'];

    public function achievement()
    {
        return $this->belongsTo(Achievement::class);
    }
}
