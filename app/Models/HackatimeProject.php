<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HackatimeProject extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'external_id',
        'url',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'hackatime_project_project')->withTimestamps();
    }
}
