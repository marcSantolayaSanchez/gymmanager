<?php

namespace App\Http\Controllers;

use App\Models\Membership;
use Illuminate\Http\Request;

class MembershipController extends Controller
{
    public function index()
    {
        $memberships = Membership::withCount(['clients as clients_count' => fn($q) => $q->active()])->get();
        return view('memberships.index', compact('memberships'));
    }

    public function create()
    {
        abort_unless(auth()->user()->isAdmin(), 403);
        return view('memberships.form');
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->isAdmin(), 403);
        $v = $request->validate(['name' => 'required|string|max:100', 'price' => 'required|numeric|min:0', 'duration_days' => 'required|integer|min:1', 'description' => 'nullable|string']);
        Membership::create($v);
        return redirect()->route('memberships.index')->with('success', 'Membresía creada correctamente.');
    }

    public function show(Membership $membership) { return redirect()->route('memberships.index'); }

    public function edit(Membership $membership)
    {
        abort_unless(auth()->user()->isAdmin(), 403);
        return view('memberships.form', compact('membership'));
    }

    public function update(Request $request, Membership $membership)
    {
        abort_unless(auth()->user()->isAdmin(), 403);
        $v = $request->validate(['name' => 'required|string|max:100', 'price' => 'required|numeric|min:0', 'duration_days' => 'required|integer|min:1', 'description' => 'nullable|string']);
        $membership->update($v);
        return redirect()->route('memberships.index')->with('success', 'Membresía actualizada.');
    }

    public function destroy(Membership $membership)
    {
        abort_unless(auth()->user()->isAdmin(), 403);
        $membership->delete();
        return redirect()->route('memberships.index')->with('success', 'Membresía eliminada.');
    }
}