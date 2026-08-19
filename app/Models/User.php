<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
        'password',
        'locale',
        'theme',
        'color_scheme',
        'role',
    ];

    public function isOwner(): bool
    {
        return $this->role === \App\Enums\UserRole::Owner;
    }

    public function isParticipant(): bool
    {
        return $this->role === \App\Enums\UserRole::Participant;
    }

    public function goals(): HasMany
    {
        return $this->hasMany(Goal::class);
    }

    public function dailyLogs(): HasMany
    {
        return $this->hasMany(DailyLog::class);
    }

    public function weeks(): HasMany
    {
        return $this->hasMany(Week::class);
    }

    public function days(): HasMany
    {
        return $this->hasMany(Day::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function recoveries(): HasMany
    {
        return $this->hasMany(Recovery::class);
    }

    public function habits(): HasMany
    {
        return $this->hasMany(Habit::class);
    }

    /** Shared arena challenges this user has joined. */
    public function sharedChallenges(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(SharedChallenge::class, 'shared_challenge_user')->withTimestamps();
    }

    /** Whether the user configured a privacy PIN. */
    public function hasPin(): bool
    {
        return $this->pin !== null;
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'pin',
        'remember_token',
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
            'pin' => 'hashed',
            'theme' => \App\Enums\Theme::class,
            'color_scheme' => \App\Enums\ColorScheme::class,
            'role' => \App\Enums\UserRole::class,
        ];
    }
}
