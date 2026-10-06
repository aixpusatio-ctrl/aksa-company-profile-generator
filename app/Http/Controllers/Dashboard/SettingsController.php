<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\MediaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

/**
 * User settings: profile, password, plan. Company level settings (domain,
 * SEO, social media) live inside each website's editor.
 */
class SettingsController extends Controller
{
    public function edit(Request $request): View
    {
        return view('dashboard.settings.edit', [
            'user' => $request->user()->load('subscription'),
            'websites' => $request->user()->companyProfiles()->orderBy('name')->get(),
            'plans' => config('platform.plans'),
        ]);
    }

    public function updateProfile(Request $request, MediaService $media): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'lowercase', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:30'],
            'avatar' => ['nullable', MediaService::imageRule(2048)],
        ]);

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $media->upload($request->file('avatar'), $user, null, 'images')->path;
        } else {
            unset($data['avatar']);
        }

        $user->update($data);

        return back()->with('success', 'Profil diperbarui.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $data = $request->validateWithBag('password', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $request->user()->update(['password' => $data['password']]);

        return back()->with('success', 'Password diperbarui.');
    }
}
