<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trainer extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'speciality', 'bio'];

    // ──────────────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────────────

    /** User → 1:1 → Trainer  (belongsTo) */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** Trainer → 1:N → Workouts  (hasMany) */
    public function workouts()
    {
        return $this->hasMany(Workout::class);
    }

    /**
     * Clients assigned to this trainer via their workouts.
     * Demonstrates: hasMany + eager loading across pivot
     */
    public function clients()
    {
        return $this->hasManyThrough(
            Client::class,
            Workout::class,
            'trainer_id',   // FK on workouts
            'id',           // FK on clients
            'id',           // PK on trainers
            'client_id'     // FK on workouts -> client
        );
    }
}
