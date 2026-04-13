<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'profile_photo',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // ──────────────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────────────

    /** @return \Illuminate\Database\Eloquent\Relations\HasOne */
    public function client()
    {
        return $this->hasOne(Client::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasOne */
    public function trainer()
    {
        return $this->hasOne(Trainer::class);
    }

    // ──────────────────────────────────────────────
    // Role helpers
    // ──────────────────────────────────────────────

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isTrainer(): bool
    {
        return $this->role === 'trainer';
    }

    public function isClient(): bool
    {
        return $this->role === 'client';
    }
}
