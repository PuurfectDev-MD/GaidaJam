<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'slack_id',
        'hackclub_id',
        'hackclub_access_token',
        'hackclub_refresh_token',
        'hackclub_token_expires_at',
        'hackatime_access_token',
        'hackatime_refresh_token',
        'hackatime_token_expires_at',
        'hackatime_user_id',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'hackclub_access_token',
        'hackclub_refresh_token',
        'hackatime_access_token',
        'hackatime_refresh_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'hackclub_access_token' => 'encrypted',
            'hackclub_refresh_token' => 'encrypted',
            'hackclub_token_expires_at' => 'datetime',
            'hackatime_access_token' => 'encrypted',
            'hackatime_refresh_token' => 'encrypted',
            'hackatime_token_expires_at' => 'datetime',
        ];
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function hackatimeProjects(): HasMany
    {
        return $this->hasMany(HackatimeProject::class);
    }
}
