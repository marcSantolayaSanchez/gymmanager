<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'membership_id', 'phone', 'birth_date',
        'weight', 'height', 'membership_starts_at', 'membership_expires_at',
    ];

    protected $casts = [
        'birth_date'             => 'date',
        'membership_starts_at'   => 'date',
        'membership_expires_at'  => 'date',
    ];

    // ──────────────────────────────────────────────
    // Relationships  (hasOne / belongsTo / hasMany)
    // ──────────────────────────────────────────────

    /** User → 1:1 → Client  (belongsTo) */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** Client → N:1 → Membership  (belongsTo) */
    public function membership()
    {
        return $this->belongsTo(Membership::class);
    }

    /** Client → 1:N → Workouts  (hasMany) */
    public function workouts()
    {
        return $this->hasMany(Workout::class);
    }

    // ──────────────────────────────────────────────
    // Membership status helpers
    // ──────────────────────────────────────────────

    /**
     * @return 'active' | 'expiring' | 'expired' | 'none'
     */
    public function membershipStatus(): string
    {
        if (! $this->membership_expires_at) return 'none';

        $today = Carbon::today();
        $expires = $this->membership_expires_at;

        if ($expires->isPast()) return 'expired';
        if ($expires->diffInDays($today) <= 7) return 'expiring';

        return 'active';
    }

    public function isMembershipExpiringSoon(): bool
    {
        return $this->membershipStatus() === 'expiring';
    }

    // ──────────────────────────────────────────────
    // Scopes
    // ──────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->whereDate('membership_expires_at', '>=', now());
    }

    public function scopeExpiringSoon($query, int $days = 7)
    {
        return $query->whereBetween('membership_expires_at', [now(), now()->addDays($days)]);
    }
}
