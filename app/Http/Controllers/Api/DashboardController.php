<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Trainer;
use App\Models\Workout;
use App\Models\Membership;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    /**
     * GET /api/dashboard
     * Returns all metrics for the admin dashboard.
     */
    public function index(): JsonResponse
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $totalClients    = Client::count();
        $activeClients   = Client::active()->count();
        $expiringClients = Client::expiringSoon()->count();

        // Monthly recurring revenue: sum of membership prices of active clients
        $monthlyRevenue = Client::active()
            ->with('membership')
            ->get()
            ->sum(fn($c) => $c->membership?->price ?? 0);

        // Membership distribution
        $distribution = Membership::withCount(['clients as active_clients_count' => fn($q) => $q->active()])
            ->get()
            ->map(fn($m) => [
                'id'           => $m->id,
                'name'         => $m->name,
                'price'        => $m->price,
                'active_count' => $m->active_clients_count,
            ]);

        // Clients expiring soon (for notification panel)
        $expiringSoon = Client::expiringSoon()
            ->with('user', 'membership')
            ->get()
            ->map(fn($c) => [
                'id'      => $c->id,
                'name'    => $c->user->name,
                'email'   => $c->user->email,
                'expires' => $c->membership_expires_at?->toDateString(),
                'plan'    => $c->membership?->name,
            ]);

        // Recent workouts
        $recentWorkouts = Workout::with(['trainer.user', 'client.user'])
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn($w) => [
                'id'     => $w->id,
                'title'  => $w->title,
                'status' => $w->status,
                'client' => $w->client->user->name ?? null,
                'trainer'=> $w->trainer->user->name ?? null,
            ]);

        return response()->json([
            'metrics' => [
                'total_clients'    => $totalClients,
                'active_clients'   => $activeClients,
                'expiring_clients' => $expiringClients,
                'total_trainers'   => Trainer::count(),
                'total_workouts'   => Workout::count(),
                'monthly_revenue'  => round($monthlyRevenue, 2),
            ],
            'membership_distribution' => $distribution,
            'expiring_soon'           => $expiringSoon,
            'recent_workouts'         => $recentWorkouts,
        ]);
    }
}
