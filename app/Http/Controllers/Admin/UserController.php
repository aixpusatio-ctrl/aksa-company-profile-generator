<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\Activity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('q')->trim()->toString();
        $role = $request->string('role')->toString();
        $status = $request->string('status')->toString();

        $users = User::query()
            ->withCount('companyProfiles')
            ->with('subscription')
            ->when($search, fn ($q) => $q->where(fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%")))
            ->when(in_array($role, [User::ROLE_ADMIN, User::ROLE_USER], true), fn ($q) => $q->where('role', $role))
            ->when($status === 'suspended', fn ($q) => $q->whereNotNull('suspended_at'))
            ->when($status === 'active', fn ($q) => $q->whereNull('suspended_at'))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'search', 'role', 'status'));
    }

    public function create(): View
    {
        return view('admin.users.form', ['user' => new User(['role' => User::ROLE_USER])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'lowercase', 'max:255', 'unique:users'],
            'role' => ['required', Rule::in([User::ROLE_ADMIN, User::ROLE_USER])],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = User::query()->create($data);
        $user->forceFill(['email_verified_at' => now()])->save();
        Activity::log('admin.user_created', "Admin membuat user {$user->email}", $user);

        return redirect()->route('admin.users.show', $user)->with('success', 'User dibuat.');
    }

    public function show(User $user): View
    {
        $user->load(['companyProfiles.template', 'companyProfiles.primaryDomain', 'subscription'])->loadCount('media');

        return view('admin.users.show', [
            'user' => $user,
            'activity' => $user->activityLogs()->latest('created_at')->take(10)->get(),
        ]);
    }

    public function edit(User $user): View
    {
        return view('admin.users.form', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'lowercase', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', Rule::in([User::ROLE_ADMIN, User::ROLE_USER])],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ]);

        if ($user->is($request->user()) && $data['role'] !== User::ROLE_ADMIN) {
            return back()->withErrors(['role' => 'Anda tidak dapat menurunkan role akun sendiri.']);
        }

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        $user->update($data);
        Activity::log('admin.user_updated', "Admin memperbarui user {$user->email}", $user);

        return redirect()->route('admin.users.show', $user)->with('success', 'User diperbarui.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        abort_if($user->is($request->user()), 422, 'Tidak dapat menghapus akun sendiri.');

        Activity::log('admin.user_deleted', "Admin menghapus user {$user->email}", null, ['email' => $user->email]);
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User dihapus.');
    }

    public function suspend(Request $request, User $user): RedirectResponse
    {
        abort_if($user->is($request->user()), 422, 'Tidak dapat menangguhkan akun sendiri.');

        $user->forceFill(['suspended_at' => now()])->save();
        Activity::log('admin.user_suspended', "User {$user->email} ditangguhkan", $user);

        return back()->with('success', 'User ditangguhkan. Website miliknya tidak lagi dapat diakses publik.');
    }

    public function unsuspend(User $user): RedirectResponse
    {
        $user->forceFill(['suspended_at' => null])->save();
        Activity::log('admin.user_unsuspended', "User {$user->email} diaktifkan kembali", $user);

        return back()->with('success', 'User diaktifkan kembali.');
    }

    public function resetPassword(User $user): RedirectResponse
    {
        $password = Str::password(12, symbols: false);
        $user->update(['password' => $password]);
        Activity::log('admin.password_reset', "Password {$user->email} direset oleh admin", $user);

        return back()->with('success', "Password baru untuk {$user->email}: {$password} — bagikan secara aman.");
    }
}
