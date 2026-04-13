<?php
namespace App\Http\Controllers;

use App\Models\Trainer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TrainerController extends Controller
{
    public function index()
    {
        $trainers = Trainer::with('user')->withCount('workouts')->get();
        return view('trainers.index', compact('trainers'));
    }

    public function create()
    {
        abort_unless(auth()->user()->isAdmin(), 403);
        return view('trainers.form');
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->isAdmin(), 403);
        $v = $request->validate([
            'name' => 'required|string|max:255', 'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8', 'speciality' => 'required|string|max:100',
            'bio' => 'nullable|string',
        ]);
        $user = User::create(['name' => $v['name'], 'email' => $v['email'], 'password' => Hash::make($v['password']), 'role' => 'trainer']);
        Trainer::create(['user_id' => $user->id, 'speciality' => $v['speciality'], 'bio' => $v['bio'] ?? null]);
        return redirect()->route('trainers.index')->with('success', 'Entrenador creado correctamente.');
    }

    public function show(Trainer $trainer) { return redirect()->route('trainers.index'); }

    public function edit(Trainer $trainer)
    {
        abort_unless(auth()->user()->isAdmin() || auth()->user()->trainer?->id === $trainer->id, 403);
        return view('trainers.form', compact('trainer'));
    }

    public function update(Request $request, Trainer $trainer)
    {
        abort_unless(auth()->user()->isAdmin() || auth()->user()->trainer?->id === $trainer->id, 403);
        $v = $request->validate(['speciality' => 'required|string|max:100', 'bio' => 'nullable|string']);
        $trainer->update($v);
        return redirect()->route('trainers.index')->with('success', 'Entrenador actualizado.');
    }

    public function destroy(Trainer $trainer)
    {
        abort_unless(auth()->user()->isAdmin(), 403);
        $trainer->user->delete();
        return redirect()->route('trainers.index')->with('success', 'Entrenador eliminado.');
    }
}
