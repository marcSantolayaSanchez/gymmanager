<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Workout;

class WorkoutPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // filtered in controller by role
    }

    public function view(User $user, Workout $workout): bool
    {
        if ($user->isAdmin()) return true;
        if ($user->isTrainer()) return $workout->trainer_id === $user->trainer?->id;
        return $workout->client_id === $user->client?->id;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'trainer']);
    }

    public function update(User $user, Workout $workout): bool
    {
        if ($user->isAdmin()) return true;
        return $user->isTrainer() && $workout->trainer_id === $user->trainer?->id;
    }

    public function delete(User $user, Workout $workout): bool
    {
        if ($user->isAdmin()) return true;
        return $user->isTrainer() && $workout->trainer_id === $user->trainer?->id;
    }
}
