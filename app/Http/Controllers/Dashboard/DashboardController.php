<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\CompanyProfile;
use App\Models\ContactMessage;
use App\Models\PageView;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $websites = $user->companyProfiles()->with(['template', 'primaryDomain'])->latest('updated_at')->get();
        $ids = $websites->pluck('id');

        return view('dashboard.index', [
            'websites' => $websites,
            'stats' => [
                'total' => $websites->count(),
                'published' => $websites->where('status', CompanyProfile::STATUS_PUBLISHED)->count(),
                'draft' => $websites->where('status', CompanyProfile::STATUS_DRAFT)->count(),
                'templates' => $websites->pluck('template_id')->filter()->unique()->count(),
                'views' => PageView::query()->whereIn('company_profile_id', $ids)->where('viewed_on', '>=', now()->subDays(29)->toDateString())->count(),
                'messages' => ContactMessage::query()->whereIn('company_profile_id', $ids)->whereNull('read_at')->count(),
            ],
            'recentMessages' => ContactMessage::query()->whereIn('company_profile_id', $ids)->with('companyProfile')->latest()->take(5)->get(),
        ]);
    }
}
