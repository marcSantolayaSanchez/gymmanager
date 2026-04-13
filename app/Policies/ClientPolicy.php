<?php

namespace App\Policies;

use App\Models\Client;
use App\Models\User;

class ClientPolicy
{
    /**
     * Admin can view all clients.
     * Trainer can view their own clients.
     * Client can only view themselves.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'trainer']);
    }

    public function view(User $user, Client $client): bool
    {
        if ($user->isAdmin()) return true;
        if ($user->isTrainer()) {
            // Trainer can view clients in their workouts
            return $client->workouts()->where('trainer_id', $user->trainer?->id)->exists();
        }
        // Client can view only themselves
        return $user->client?->id === $client->id;
    }

    /** Only admin can create clients */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /** Admin always, trainer only their clients, client only themselves */
    public function update(User $user, Client $client): bool
    {
        if ($user->isAdmin()) return true;
        return $user->client?->id === $client->id;
    }

    /** Only admin can delete */
    public function delete(User $user, Client $client): bool
    {
        return $user->isAdmin();
    }
}
