<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\TrainerController;
use App\Http\Controllers\WorkoutController;
use App\Http\Controllers\MembershipController;

// ── Auth ──────────────────────────────────────────────────────────────────────
Route::get('/login', fn() => view('auth.login'))->name('login')->middleware('guest');
Route::post('/login', function (\Illuminate\Http\Request $request) {
    $credentials = $request->validate(['email' => 'required|email', 'password' => 'required']);
    if (\Illuminate\Support\Facades\Auth::attempt($credentials, $request->boolean('remember'))) {
        $request->session()->regenerate();
        return redirect()->intended(route('dashboard'));
    }
    return back()->withErrors(['email' => 'Las credenciales no son correctas.'])->onlyInput('email');
});
Route::post('/logout', function (\Illuminate\Http\Request $request) {
    \Illuminate\Support\Facades\Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect()->route('login');
})->name('logout')->middleware('auth');

// ── Protected ─────────────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/', fn() => redirect()->route('dashboard'));
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('clients',     ClientController::class);
    Route::resource('trainers',    TrainerController::class);
    Route::resource('workouts',    WorkoutController::class);
    Route::resource('memberships', MembershipController::class);

    Route::post('clients/{client}/assign-membership', [ClientController::class, 'assignMembership'])
         ->name('clients.assign-membership');
});
