<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Workout;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class WorkoutController extends Controller
{
    /**
     * GET /api/workouts
     * Eager loading: trainer.user + client.user + client.membership
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Workout::class);

        $user = auth()->user();

        $query = Workout::with(['trainer.user', 'client.user', 'client.membership'])
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->latest();

        if ($user->isTrainer()) {
            $query->where('trainer_id', $user->trainer?->id);
        } elseif ($user->isClient()) {
            $query->where('client_id', $user->client?->id);
        }

        return response()->json($query->paginate(20)->map(fn($w) => $this->format($w)));
    }

    /**
     * POST /api/workouts
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Workout::class);

        $validated = $request->validate([
            'client_id'      => 'required|exists:clients,id',
            'trainer_id'     => 'required|exists:trainers,id',
            'title'          => 'required|string|max:255',
            'description'    => 'nullable|string',
            'scheduled_date' => 'nullable|date',
        ]);

        // Trainer can only create workouts assigned to themselves
        if (auth()->user()->isTrainer()) {
            $validated['trainer_id'] = auth()->user()->trainer->id;
        }

        $workout = Workout::create($validated);
        $workout->load(['trainer.user', 'client.user']);

        return response()->json($this->format($workout), 201);
    }

    /**
     * GET /api/workouts/{id}
     */
    public function show(Workout $workout): JsonResponse
    {
        $this->authorize('view', $workout);

        $workout->load(['trainer.user', 'client.user', 'client.membership']);

        return response()->json($this->format($workout));
    }

    /**
     * PUT /api/workouts/{id}
     */
    public function update(Request $request, Workout $workout): JsonResponse
    {
        $this->authorize('update', $workout);

        $validated = $request->validate([
            'title'          => 'sometimes|string|max:255',
            'description'    => 'nullable|string',
            'scheduled_date' => 'nullable|date',
            'status'         => 'sometimes|in:pending,completed,cancelled',
        ]);

        $workout->update($validated);

        return response()->json($this->format($workout->fresh(['trainer.user', 'client.user'])));
    }

    /**
     * DELETE /api/workouts/{id}
     */
    public function destroy(Workout $workout): JsonResponse
    {
        $this->authorize('delete', $workout);

        $workout->delete();

        return response()->json(['message' => 'Rutina eliminada correctamente.']);
    }

    private function format(Workout $w): array
    {
        return [
            'id'             => $w->id,
            'title'          => $w->title,
            'description'    => $w->description,
            'status'         => $w->status,
            'scheduled_date' => $w->scheduled_date?->toDateString(),
            'trainer'        => $w->trainer ? [
                'id'   => $w->trainer->id,
                'name' => $w->trainer->user->name,
            ] : null,
            'client'         => $w->client ? [
                'id'         => $w->client->id,
                'name'       => $w->client->user->name,
                'membership' => $w->client->membership?->name,
            ] : null,
        ];
    }
}
