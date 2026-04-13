<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Trainer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TrainerController extends Controller
{
    /**
     * GET /api/trainers
     * Eager loading: user + workouts count + distinct clients
     */
    public function index(): JsonResponse
    {
        $trainers = Trainer::with(['user', 'workouts'])
            ->withCount('workouts')
            ->get();

        return response()->json($trainers->map(fn($t) => $this->format($t)));
    }

    /**
     * POST /api/trainers  (admin only)
     */
    public function store(Request $request): JsonResponse
    {
        abort_unless(auth()->user()->isAdmin(), 403, 'Solo el administrador puede crear entrenadores.');

        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email',
            'password'   => 'required|string|min:8',
            'speciality' => 'required|string|max:100',
            'bio'        => 'nullable|string',
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => $validated['password'],
            'role'     => 'trainer',
        ]);

        $trainer = Trainer::create([
            'user_id'    => $user->id,
            'speciality' => $validated['speciality'],
            'bio'        => $validated['bio'] ?? null,
        ]);

        return response()->json($this->format($trainer->load('user')), 201);
    }

    /**
     * GET /api/trainers/{id}
     */
    public function show(Trainer $trainer): JsonResponse
    {
        $trainer->load(['user', 'workouts.client.user']);

        return response()->json($this->format($trainer, detailed: true));
    }

    /**
     * PUT /api/trainers/{id}
     */
    public function update(Request $request, Trainer $trainer): JsonResponse
    {
        $user = auth()->user();
        abort_unless(
            $user->isAdmin() || $user->trainer?->id === $trainer->id,
            403
        );

        $validated = $request->validate([
            'speciality' => 'sometimes|string|max:100',
            'bio'        => 'nullable|string',
        ]);

        $trainer->update($validated);

        return response()->json($this->format($trainer->fresh('user')));
    }

    /**
     * DELETE /api/trainers/{id}  (admin only)
     */
    public function destroy(Trainer $trainer): JsonResponse
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $trainer->user->delete();

        return response()->json(['message' => 'Entrenador eliminado correctamente.']);
    }

    private function format(Trainer $t, bool $detailed = false): array
    {
        $data = [
            'id'            => $t->id,
            'name'          => $t->user->name,
            'email'         => $t->user->email,
            'speciality'    => $t->speciality,
            'bio'           => $t->bio,
            'workouts_count'=> $t->workouts_count ?? $t->workouts->count(),
        ];

        if ($detailed) {
            $data['workouts'] = $t->workouts->map(fn($w) => [
                'id'     => $w->id,
                'title'  => $w->title,
                'status' => $w->status,
                'client' => $w->client->user->name ?? null,
            ]);
        }

        return $data;
    }
}
