<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workout extends Model
{
    use HasFactory;

    protected $fillable = [
        'trainer_id', 'client_id', 'title', 'description', 'scheduled_date', 'status',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
    ];

    /** Workout → belongsTo → Trainer */
    public function trainer()
    {
        return $this->belongsTo(Trainer::class)->with('user');
    }

    /** Workout → belongsTo → Client */
    public function client()
    {
        return $this->belongsTo(Client::class)->with('user');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}
