<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\Activity;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        abort_unless((bool) setting('registration_enabled', true), 404);

        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless((bool) setting('registration_enabled', true), 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Password::defaults()],
            'terms' => ['accepted'],
        ]);

        $user = User::query()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => User::ROLE_USER,
        ]);

        // Every new account starts with a Professional trial.
        $user->subscriptions()->create([
            'plan' => 'pro',
            'status' => 'trialing',
            'price' => 0,
            'starts_at' => now(),
            'trial_ends_at' => now()->addDays(config('platform.trial_days')),
        ]);

        event(new Registered($user));
        Activity::log('auth.registered', "{$user->name} mendaftar", $user, [], $user->id);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('success', 'Selamat datang! Akun Anda berhasil dibuat.');
    }
}
