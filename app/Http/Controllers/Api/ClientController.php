<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ClientController extends Controller
{
    /**
     * GET /api/clients
     * Eager loading: user + membership + workouts.trainer.user
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Client::class);

        $query = Client::with(['user', 'membership', 'workouts.trainer.user'])
            ->when($request->status === 'active', fn($q) => $q->active())
            ->when($request->status === 'expiring', fn($q) => $q->expiringSoon())
            ->when($request->search, fn($q, $s) =>
                $q->whereHas('user', fn($u) => $u->where('name', 'like', "%$s%"))
            );

        // Trainer sees only their clients
        if (auth()->user()->isTrainer()) {
            $trainerId = auth()->user()->trainer?->id;
            $query->whereHas('workouts', fn($q) => $q->where('trainer_id', $trainerId));
        }

        $clients = $query->paginate(15);

        return response()->json([
            'data' => $clients->map(fn($c) => $this->format($c)),
            'meta' => [
                'total'        => $clients->total(),
                'current_page' => $clients->currentPage(),
                'last_page'    => $clients->lastPage(),
            ]
        ]);
    }

    /**
     * POST /api/clients
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Client::class);

        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|unique:users,email',
            'password'    => 'required|string|min:8',
            'phone'       => 'nullable|string|max:20',
            'birth_date'  => 'nullable|date',
            'weight'      => 'nullable|numeric|min:20|max:300',
            'height'      => 'nullable|numeric|min:100|max:250',
            'membership_id' => 'nullable|exists:memberships,id',
        ]);

        $user = \App\Models\User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => $validated['password'],
            'role'     => 'client',
        ]);

        $membershipExpires = null;
        if (! empty($validated['membership_id'])) {
            $membership = \App\Models\Membership::find($validated['membership_id']);
            $membershipExpires = now()->addDays($membership->duration_days);
        }

        $client = Client::create([
            'user_id'                => $user->id,
            'phone'                  => $validated['phone'] ?? null,
            'birth_date'             => $validated['birth_date'] ?? null,
            'weight'                 => $validated['weight'] ?? null,
            'height'                 => $validated['height'] ?? null,
            'membership_id'          => $validated['membership_id'] ?? null,
            'membership_starts_at'   => $validated['membership_id'] ? now() : null,
            'membership_expires_at'  => $membershipExpires,
        ]);

        $client->load(['user', 'membership']);

        return response()->json($this->format($client), 201);
    }

    /**
     * GET /api/clients/{id}
     */
    public function show(Client $client): JsonResponse
    {
        $this->authorize('view', $client);

        $client->load(['user', 'membership', 'workouts' => fn($q) => $q->with('trainer.user')->latest()]);

        return response()->json($this->format($client, detailed: true));
    }

    /**
     * PUT /api/clients/{id}
     */
    public function update(Request $request, Client $client): JsonResponse
    {
        $this->authorize('update', $client);

        $validated = $request->validate([
            'phone'         => 'nullable|string|max:20',
            'birth_date'    => 'nullable|date',
            'weight'        => 'nullable|numeric',
            'height'        => 'nullable|numeric',
            'membership_id' => 'nullable|exists:memberships,id',
        ]);

        // If membership changed, recalculate expiry
        if (isset($validated['membership_id']) && $validated['membership_id'] !== $client->membership_id) {
            $membership = \App\Models\Membership::find($validated['membership_id']);
            $validated['membership_starts_at']  = now();
            $validated['membership_expires_at'] = now()->addDays($membership->duration_days);
        }

        $client->update($validated);

        return response()->json($this->format($client->fresh(['user', 'membership'])));
    }

    /**
     * DELETE /api/clients/{id}
     */
    public function destroy(Client $client): JsonResponse
    {
        $this->authorize('delete', $client);

        $client->user->delete(); // Cascades to client via DB constraint

        return response()->json(['message' => 'Cliente eliminado correctamente.']);
    }

    // ──────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────

    private function format(Client $c, bool $detailed = false): array
    {
        $data = [
            'id'                    => $c->id,
            'name'                  => $c->user->name,
            'email'                 => $c->user->email,
            'phone'                 => $c->phone,
            'birth_date'            => $c->birth_date?->toDateString(),
            'weight'                => $c->weight,
            'height'                => $c->height,
            'membership'            => $c->membership ? [
                'id'    => $c->membership->id,
                'name'  => $c->membership->name,
                'price' => $c->membership->price,
            ] : null,
            'membership_status'     => $c->membershipStatus(),
            'membership_expires_at' => $c->membership_expires_at?->toDateString(),
        ];

        if ($detailed) {
            $data['workouts'] = $c->workouts->map(fn($w) => [
                'id'     => $w->id,
                'title'  => $w->title,
                'status' => $w->status,
                'date'   => $w->scheduled_date?->toDateString(),
                'trainer'=> $w->trainer->user->name ?? null,
            ]);
        }

        return $data;
    }
}
