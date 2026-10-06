<?php

namespace App\Services;

use App\Models\CompanyProfile;
use App\Models\PageView;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Throwable;

/**
 * Lightweight, privacy friendly page view tracking for tenant websites
 * (no cookies, visitor identified by a daily salted hash).
 */
class AnalyticsService
{
    public function record(CompanyProfile $company, Request $request): void
    {
        $agent = (string) $request->userAgent();

        if ($agent === '' || Str::contains(Str::lower($agent), ['bot', 'crawl', 'spider', 'preview', 'curl'])) {
            return;
        }

        try {
            PageView::query()->create([
                'company_profile_id' => $company->getKey(),
                'path' => Str::limit('/'.ltrim($request->path(), '/'), 250, ''),
                'referrer' => Str::limit((string) $request->headers->get('referer'), 490, '') ?: null,
                'visitor_hash' => hash('sha256', $request->ip().$agent.now()->toDateString().config('app.key')),
                'viewed_on' => now()->toDateString(),
            ]);
        } catch (Throwable $e) {
            report($e);
        }
    }

    /**
     * Daily views & unique visitors for the last N days.
     */
    public function stats(CompanyProfile $company, int $days = 30): array
    {
        $from = now()->subDays($days - 1)->startOfDay();

        $rows = PageView::query()
            ->where('company_profile_id', $company->getKey())
            ->where('viewed_on', '>=', $from->toDateString())
            ->selectRaw('viewed_on, count(*) as views, count(distinct visitor_hash) as visitors')
            ->groupBy('viewed_on')
            ->get()
            ->keyBy(fn ($row) => Carbon::parse($row->viewed_on)->toDateString());

        $series = collect(range(0, $days - 1))->map(function ($i) use ($from, $rows) {
            $date = $from->copy()->addDays($i)->toDateString();

            return [
                'date' => $date,
                'views' => (int) ($rows[$date]->views ?? 0),
                'visitors' => (int) ($rows[$date]->visitors ?? 0),
            ];
        });

        $topPages = PageView::query()
            ->where('company_profile_id', $company->getKey())
            ->where('viewed_on', '>=', $from->toDateString())
            ->selectRaw('path, count(*) as views')
            ->groupBy('path')
            ->orderByDesc('views')
            ->limit(5)
            ->get();

        return [
            'series' => $series,
            'total_views' => $series->sum('views'),
            'total_visitors' => $series->sum('visitors'),
            'top_pages' => $topPages,
        ];
    }
}
