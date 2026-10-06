<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\User;
use App\Support\Activity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    public function index(Request $request): View
    {
        $plan = $request->string('plan')->toString();

        $users = User::query()
            ->where('role', User::ROLE_USER)
            ->with('subscription')
            ->withCount('companyProfiles')
            ->when($plan === 'free', fn ($q) => $q->whereDoesntHave('subscriptions'))
            ->when($plan && $plan !== 'free', fn ($q) => $q->whereHas('subscription', fn ($s) => $s->where('plan', $plan)))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $mrr = Subscription::query()->where('status', 'active')->sum('price');

        return view('admin.subscriptions.index', [
            'users' => $users,
            'plans' => config('platform.plans'),
            'plan' => $plan,
            'mrr' => $mrr,
            'counts' => Subscription::query()->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status'),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'plan' => ['required', Rule::in(array_keys(config('platform.plans')))],
            'status' => ['required', Rule::in(Subscription::STATUSES)],
            'ends_at' => ['nullable', 'date'],
        ]);

        $user->subscriptions()->create([
            'plan' => $data['plan'],
            'status' => $data['status'],
            'price' => config("platform.plans.{$data['plan']}.price", 0),
            'starts_at' => now(),
            'ends_at' => $data['ends_at'] ?? null,
            'trial_ends_at' => $data['status'] === 'trialing' ? now()->addDays(config('platform.trial_days')) : null,
        ]);

        Activity::log('admin.subscription', "Langganan {$user->email} → {$data['plan']} ({$data['status']})", $user);

        return back()->with('success', 'Langganan diperbarui.');
    }
}
