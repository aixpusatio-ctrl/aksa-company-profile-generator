<?php

namespace App\Providers;

use App\Models\CompanyProfile;
use App\Services\Domains\DnsDomainVerifier;
use App\Services\Domains\DomainVerifier;
use App\Services\Domains\FakeDomainVerifier;
use App\Services\SettingService;
use App\Support\CurrentTenant;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SettingService::class);
        $this->app->scoped(CurrentTenant::class);

        $this->app->bind(DomainVerifier::class, fn () => config('platform.custom_domains.verifier') === 'dns'
            ? new DnsDomainVerifier
            : new FakeDomainVerifier);
    }

    public function boot(): void
    {
        Route::model('company', CompanyProfile::class);

        RateLimiter::for('contact', fn (Request $request) => [
            Limit::perMinute(5)->by('contact:'.$request->ip()),
            Limit::perDay(50)->by('contact-day:'.$request->ip()),
        ]);
        RateLimiter::for('register', fn (Request $request) => Limit::perHour(10)->by($request->ip()));
        RateLimiter::for('autosave', fn (Request $request) => Limit::perMinute(60)->by($request->user()?->id ?: $request->ip()));
        RateLimiter::for('uploads', fn (Request $request) => Limit::perMinute(30)->by($request->user()?->id ?: $request->ip()));
        RateLimiter::for('api', fn (Request $request) => Limit::perMinute(60)->by($request->ip()));

        View::composer(['layouts.*', 'components.layouts.*'], function ($view) {
            $view->with('appName', app_name());
        });
    }
}
