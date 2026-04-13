<?php
// ============================================================
// DashboardController.php  (Web)
// ============================================================
namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Trainer;
use App\Models\Workout;
use App\Models\Membership;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $metrics = [
            'total_clients'    => Client::count(),
            'active_clients'   => Client::active()->count(),
            'expiring_clients' => Client::expiringSoon()->count(),
            'total_trainers'   => Trainer::count(),
            'total_workouts'   => Workout::count(),
            'monthly_revenue'  => Client::active()->with('membership')->get()->sum(fn($c) => $c->membership?->price ?? 0),
        ];

        $recentClients = Client::with(['user', 'membership', 'workouts.trainer.user'])->latest()->limit(5)->get();
        $recentWorkouts = Workout::with(['trainer.user', 'client.user'])->latest()->limit(4)->get();
        $expiringSoon = Client::expiringSoon()->with(['user', 'membership'])->get();
        $membershipDistribution = Membership::withCount(['clients as active_count' => fn($q) => $q->active()])->get();

        return view('dashboard.index', compact('metrics', 'recentClients', 'recentWorkouts', 'expiringSoon', 'membershipDistribution'));
    }
}
