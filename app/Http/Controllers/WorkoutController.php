<?php
namespace App\Http\Controllers;

use App\Models\Workout;
use App\Models\Client;
use App\Models\Trainer;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class WorkoutController extends Controller
{
    use AuthorizesRequests;
    public function index(Request $request)
    {
        $query = Workout::with(['trainer.user', 'client.user'])
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->latest();

        if (auth()->user()->isTrainer()) $query->where('trainer_id', auth()->user()->trainer?->id);
        if (auth()->user()->isClient())  $query->where('client_id',  auth()->user()->client?->id);

        return view('workouts.index', ['workouts' => $query->paginate(20)]);
    }

    public function create()
    {
        $this->authorize('create', Workout::class);
        return view('workouts.form', [
            'clients'  => Client::with('user')->get(),
            'trainers' => Trainer::with('user')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Workout::class);
        $v = $request->validate([
            'client_id' => 'required|exists:clients,id', 'trainer_id' => 'required|exists:trainers,id',
            'title' => 'required|string|max:255', 'description' => 'nullable|string',
            'scheduled_date' => 'nullable|date',
        ]);
        if (auth()->user()->isTrainer()) $v['trainer_id'] = auth()->user()->trainer->id;
        Workout::create($v);
        return redirect()->route('workouts.index')->with('success', 'Rutina creada correctamente.');
    }

    public function show(Workout $workout) { return redirect()->route('workouts.index'); }

    public function edit(Workout $workout)
    {
        $this->authorize('update', $workout);
        return view('workouts.form', [
            'workout'  => $workout,
            'clients'  => Client::with('user')->get(),
            'trainers' => Trainer::with('user')->get(),
        ]);
    }

    public function update(Request $request, Workout $workout)
    {
        $this->authorize('update', $workout);
        $v = $request->validate([
            'title' => 'required|string|max:255', 'description' => 'nullable|string',
            'scheduled_date' => 'nullable|date', 'status' => 'sometimes|in:pending,completed,cancelled',
            'client_id' => 'required|exists:clients,id', 'trainer_id' => 'required|exists:trainers,id',
        ]);
        $workout->update($v);
        return redirect()->route('workouts.index')->with('success', 'Rutina actualizada.');
    }

    public function destroy(Workout $workout)
    {
        $this->authorize('delete', $workout);
        $workout->delete();
        return redirect()->route('workouts.index')->with('success', 'Rutina eliminada.');
    }
}

