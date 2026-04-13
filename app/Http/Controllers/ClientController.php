<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Membership;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ClientController extends Controller
{
    use AuthorizesRequests;
    public function index(Request $request)
    {
        $this->authorize('viewAny', Client::class);

        $query = Client::with(['user', 'membership', 'workouts'])
            ->when($request->search, fn($q, $s) =>
                $q->whereHas('user', fn($u) => $u->where('name', 'like', "%$s%")->orWhere('email', 'like', "%$s%"))
            )
            ->when($request->status === 'active',   fn($q) => $q->active())
            ->when($request->status === 'expiring', fn($q) => $q->expiringSoon())
            ->when($request->status === 'expired',  fn($q) => $q->whereDate('membership_expires_at', '<', now()))
            ->latest();

        if (auth()->user()->isTrainer()) {
            $trainerId = auth()->user()->trainer?->id;
            $query->whereHas('workouts', fn($q) => $q->where('trainer_id', $trainerId));
        }

        return view('clients.index', ['clients' => $query->paginate(15)]);
    }

    public function create()
    {
        $this->authorize('create', Client::class);
        return view('clients.form', ['memberships' => Membership::active()->get()]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Client::class);

        $v = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            'password'      => 'required|string|min:8',
            'phone'         => 'nullable|string|max:20',
            'birth_date'    => 'nullable|date',
            'weight'        => 'nullable|numeric',
            'height'        => 'nullable|numeric',
            'membership_id' => 'nullable|exists:memberships,id',
        ]);

        $user = User::create(['name' => $v['name'], 'email' => $v['email'], 'password' => Hash::make($v['password']), 'role' => 'client']);

        $expires = null;
        if (!empty($v['membership_id'])) {
            $m = Membership::find($v['membership_id']);
            $expires = now()->addDays($m->duration_days);
        }

        Client::create([
            'user_id' => $user->id, 'phone' => $v['phone'] ?? null,
            'birth_date' => $v['birth_date'] ?? null, 'weight' => $v['weight'] ?? null,
            'height' => $v['height'] ?? null, 'membership_id' => $v['membership_id'] ?? null,
            'membership_starts_at' => $v['membership_id'] ? now() : null,
            'membership_expires_at' => $expires,
        ]);

        return redirect()->route('clients.index')->with('success', 'Cliente creado correctamente.');
    }

    public function show(Client $client)
    {
        $this->authorize('view', $client);
        $client->load(['user', 'membership', 'workouts.trainer.user']);
        return view('clients.show', ['client' => $client, 'memberships' => Membership::active()->get()]);
    }

    public function edit(Client $client)
    {
        $this->authorize('update', $client);
        return view('clients.form', ['client' => $client, 'memberships' => Membership::active()->get()]);
    }

    public function update(Request $request, Client $client)
    {
        $this->authorize('update', $client);

        $v = $request->validate([
            'phone' => 'nullable|string|max:20', 'birth_date' => 'nullable|date',
            'weight' => 'nullable|numeric', 'height' => 'nullable|numeric',
            'membership_id' => 'nullable|exists:memberships,id',
        ]);

        if (isset($v['membership_id']) && $v['membership_id'] != $client->membership_id) {
            $m = Membership::find($v['membership_id']);
            $v['membership_starts_at'] = now();
            $v['membership_expires_at'] = now()->addDays($m->duration_days);
        }

        $client->update($v);
        return redirect()->route('clients.show', $client)->with('success', 'Cliente actualizado correctamente.');
    }

    public function destroy(Client $client)
    {
        $this->authorize('delete', $client);
        $client->user->delete();
        return redirect()->route('clients.index')->with('success', 'Cliente eliminado correctamente.');
    }

    public function assignMembership(Request $request, Client $client)
    {
        $this->authorize('update', $client);
        $v = $request->validate(['membership_id' => 'nullable|exists:memberships,id']);

        $expires = null;
        if (!empty($v['membership_id'])) {
            $m = Membership::find($v['membership_id']);
            $expires = now()->addDays($m->duration_days);
        }

        $client->update(['membership_id' => $v['membership_id'], 'membership_starts_at' => now(), 'membership_expires_at' => $expires]);
        return redirect()->back()->with('success', 'Membresía asignada correctamente.');
    }
}
