<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\CompanyProfile;
use App\Models\Domain;
use App\Models\Shop\Order;
use App\Models\Template;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $since = now()->subDays(30);

        $days = collect(range(13, 0))->map(fn ($i) => now()->subDays($i)->toDateString());
        $signups = User::query()->where('created_at', '>=', now()->subDays(13)->startOfDay())->get(['created_at'])
            ->groupBy(fn ($u) => Carbon::parse($u->created_at)->toDateString())->map->count();
        $sites = CompanyProfile::query()->where('created_at', '>=', now()->subDays(13)->startOfDay())->get(['created_at'])
            ->groupBy(fn ($c) => Carbon::parse($c->created_at)->toDateString())->map->count();

        return view('admin.dashboard', [
            'stats' => [
                'users' => User::query()->count(),
                'websites' => CompanyProfile::query()->count(),
                'published' => CompanyProfile::query()->published()->count(),
                'domains' => Domain::query()->active()->count(),
                'templates' => Template::query()->count(),
                'new_users' => User::query()->where('created_at', '>=', $since)->count(),
                'new_websites' => CompanyProfile::query()->where('created_at', '>=', $since)->count(),
                'pending_domains' => Domain::query()->whereIn('status', [Domain::STATUS_PENDING, Domain::STATUS_VERIFYING])->count(),
            ],
            'shopStats' => [
                'shops' => CompanyProfile::query()->where('shop_enabled', true)->count(),
                'orders' => Order::query()->count(),
                'open_orders' => Order::query()->whereNotIn('status', ['completed', 'cancelled', 'refunded'])->count(),
                'gmv' => (float) Order::query()->where('payment_status', 'paid')->whereNotIn('status', ['cancelled', 'refunded'])->sum('total'),
            ],
            'chart' => $days->map(fn ($d) => ['date' => $d, 'users' => $signups[$d] ?? 0, 'websites' => $sites[$d] ?? 0]),
            'latestUsers' => User::query()->latest()->take(5)->get(),
            'latestWebsites' => CompanyProfile::query()->with(['user', 'template'])->latest()->take(5)->get(),
            'popularTemplates' => Template::query()->withCount('companyProfiles')->orderByDesc('company_profiles_count')->take(5)->get(),
            'activity' => ActivityLog::query()->with('user')->latest('created_at')->take(8)->get(),
        ]);
    }
}
