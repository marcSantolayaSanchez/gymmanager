<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Membership extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'price', 'duration_days', 'description', 'active'];

    protected $casts = ['active' => 'boolean'];

    /** Membership → 1:N → Clients */
    public function clients()
    {
        return $this->hasMany(Client::class);
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
