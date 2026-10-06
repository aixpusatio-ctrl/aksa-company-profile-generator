<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Dashboard;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\PublicTemplateController;
use App\Http\Controllers\Site\WebsiteController as SiteController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Tenant websites
|--------------------------------------------------------------------------
|
| Any host that is NOT a central domain (company.platform.test, a verified
| custom domain such as www.company.com, ...) is served by these routes.
| They are registered first so they win over the central routes below.
|
*/

$centralHosts = collect(config('platform.central_domains'))
    ->map(fn ($host) => preg_quote(strtolower($host), '#'))
    ->implode('|');

Route::domain('{tenant_host}')
    ->where(['tenant_host' => '(?!(?:'.$centralHosts.')$)[a-z0-9][a-z0-9.\-]*'])
    ->middleware(['tenant.domain', 'tenant'])
    ->name('site.')
    ->group(function () {
        Route::get('/', [SiteController::class, 'home'])->name('home');
        Route::get('/sitemap.xml', [SiteController::class, 'sitemap'])->name('sitemap');
        Route::get('/robots.txt', [SiteController::class, 'robots'])->name('robots');
        Route::post('/contact', [SiteController::class, 'contact'])->middleware('throttle:contact')->name('contact');
        Route::get('/{slug}', [SiteController::class, 'page'])->where('slug', '[a-z0-9\-]+')->name('page');
    });

/*
|--------------------------------------------------------------------------
| Central application (landing page, auth, dashboard, admin)
|--------------------------------------------------------------------------
*/

Route::middleware('central')->group(function () {
    Route::get('/', [LandingController::class, 'index'])->name('home');
    Route::get('/robots.txt', [LandingController::class, 'robots'])->name('robots');
    Route::get('/sitemap.xml', [LandingController::class, 'sitemap'])->name('sitemap');

    Route::get('/templates', [PublicTemplateController::class, 'index'])->name('templates.index');
    Route::get('/templates/{template}', [PublicTemplateController::class, 'show'])->name('templates.show');
    Route::get('/templates/{template}/render', [PublicTemplateController::class, 'render'])->name('templates.render');

    // ------------------------------------------------------------------ Auth
    Route::middleware('guest')->group(function () {
        Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
        Route::post('/register', [RegisteredUserController::class, 'store'])->middleware('throttle:register');
        Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
        Route::post('/login', [AuthenticatedSessionController::class, 'store']);
        Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
        Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->middleware('throttle:6,1')->name('password.email');
        Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
        Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.store');
    });

    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->middleware('auth')->name('logout');

    // ------------------------------------------------------------------ User dashboard
    Route::middleware(['auth', 'active'])->prefix('dashboard')->group(function () {
        Route::get('/', [Dashboard\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/templates', [Dashboard\TemplateGalleryController::class, 'index'])->name('dashboard.templates');
        Route::get('/domains', [Dashboard\DomainController::class, 'overview'])->name('domains.index');

        Route::get('/media', [Dashboard\MediaController::class, 'index'])->name('media.index');
        Route::post('/media', [Dashboard\MediaController::class, 'store'])->middleware('throttle:uploads')->name('media.store');
        Route::delete('/media/{media}', [Dashboard\MediaController::class, 'destroy'])->name('media.destroy');

        Route::get('/notifications', [Dashboard\NotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/read', [Dashboard\NotificationController::class, 'markAllRead'])->name('notifications.read');

        Route::get('/settings', [Dashboard\SettingsController::class, 'edit'])->name('settings.edit');
        Route::put('/settings/profile', [Dashboard\SettingsController::class, 'updateProfile'])->name('settings.profile');
        Route::put('/settings/password', [Dashboard\SettingsController::class, 'updatePassword'])->name('settings.password');

        // Websites (company profiles)
        Route::get('/websites', [Dashboard\WebsiteController::class, 'index'])->name('websites.index');
        Route::get('/websites/create', [Dashboard\WebsiteController::class, 'create'])->name('websites.create');
        Route::post('/websites', [Dashboard\WebsiteController::class, 'store'])->name('websites.store');

        Route::prefix('websites/{company}')->name('websites.')->scopeBindings()->group(function () {
            Route::get('/', [Dashboard\WebsiteController::class, 'show'])->name('show');
            Route::delete('/', [Dashboard\WebsiteController::class, 'destroy'])->name('destroy');
            Route::get('/preview', [Dashboard\PreviewController::class, 'show'])->name('preview');
            Route::get('/preview/frame', [Dashboard\PreviewController::class, 'frame'])->name('preview.frame');
            Route::post('/publish', [Dashboard\WebsiteController::class, 'publish'])->name('publish');
            Route::post('/unpublish', [Dashboard\WebsiteController::class, 'unpublish'])->name('unpublish');

            // Creation wizard
            Route::get('/wizard/{step?}', [Dashboard\WizardController::class, 'show'])->name('wizard');
            Route::post('/wizard/{step}', [Dashboard\WizardController::class, 'save'])->name('wizard.save');
            Route::post('/autosave', [Dashboard\WizardController::class, 'autosave'])->middleware('throttle:autosave')->name('autosave');

            // Profile editor tabs: info, about, contact, branding, seo
            Route::get('/edit/{tab}', [Dashboard\ProfileEditorController::class, 'edit'])->name('edit');
            Route::put('/edit/{tab}', [Dashboard\ProfileEditorController::class, 'update'])->name('update');

            Route::get('/template', [Dashboard\ProfileEditorController::class, 'template'])->name('template.edit');
            Route::put('/template', [Dashboard\ProfileEditorController::class, 'updateTemplate'])->name('template.update');

            // Repeatable content: services, products, projects, team, testimonials, gallery
            Route::get('/content/{type}', [Dashboard\ContentController::class, 'index'])->name('content.index');
            Route::post('/content/{type}', [Dashboard\ContentController::class, 'store'])->name('content.store');
            Route::post('/content/{type}/reorder', [Dashboard\ContentController::class, 'reorder'])->name('content.reorder');
            Route::put('/content/{type}/{id}', [Dashboard\ContentController::class, 'update'])->whereNumber('id')->name('content.update');
            Route::delete('/content/{type}/{id}', [Dashboard\ContentController::class, 'destroy'])->whereNumber('id')->name('content.destroy');

            Route::resource('pages', Dashboard\PageController::class)->except('show');

            Route::get('/menus', [Dashboard\MenuController::class, 'index'])->name('menus.index');
            Route::post('/menus', [Dashboard\MenuController::class, 'store'])->name('menus.store');
            Route::post('/menus/tree', [Dashboard\MenuController::class, 'tree'])->name('menus.tree');
            Route::put('/menus/{menu}', [Dashboard\MenuController::class, 'update'])->name('menus.update');
            Route::delete('/menus/{menu}', [Dashboard\MenuController::class, 'destroy'])->name('menus.destroy');

            Route::get('/sections', [Dashboard\SectionController::class, 'index'])->name('sections.index');
            Route::put('/sections', [Dashboard\SectionController::class, 'update'])->name('sections.update');

            Route::get('/domains', [Dashboard\DomainController::class, 'index'])->name('domains.index');
            Route::put('/domains/subdomain', [Dashboard\DomainController::class, 'updateSubdomain'])->name('domains.subdomain');
            Route::post('/domains', [Dashboard\DomainController::class, 'store'])->name('domains.store');
            Route::post('/domains/{domain}/verify', [Dashboard\DomainController::class, 'verify'])->name('domains.verify');
            Route::post('/domains/{domain}/primary', [Dashboard\DomainController::class, 'primary'])->name('domains.primary');
            Route::delete('/domains/{domain}', [Dashboard\DomainController::class, 'destroy'])->name('domains.destroy');

            Route::get('/messages', [Dashboard\MessageController::class, 'index'])->name('messages.index');
            Route::patch('/messages/{message}', [Dashboard\MessageController::class, 'update'])->name('messages.update');
            Route::delete('/messages/{message}', [Dashboard\MessageController::class, 'destroy'])->name('messages.destroy');
        });
    });

    // ------------------------------------------------------------------ Admin panel
    Route::middleware(['auth', 'active', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');

        Route::resource('users', Admin\UserController::class);
        Route::post('users/{user}/suspend', [Admin\UserController::class, 'suspend'])->name('users.suspend');
        Route::post('users/{user}/unsuspend', [Admin\UserController::class, 'unsuspend'])->name('users.unsuspend');
        Route::post('users/{user}/reset-password', [Admin\UserController::class, 'resetPassword'])->name('users.reset-password');

        Route::get('companies', [Admin\CompanyProfileController::class, 'index'])->name('companies.index');
        Route::get('companies/{company}', [Admin\CompanyProfileController::class, 'show'])->name('companies.show');
        Route::post('companies/{company}/status', [Admin\CompanyProfileController::class, 'toggleStatus'])->name('companies.status');
        Route::delete('companies/{company}', [Admin\CompanyProfileController::class, 'destroy'])->name('companies.destroy');

        Route::resource('templates', Admin\TemplateController::class)->except('show');
        Route::post('templates/{template}/duplicate', [Admin\TemplateController::class, 'duplicate'])->name('templates.duplicate');
        Route::post('templates/{template}/publish', [Admin\TemplateController::class, 'togglePublish'])->name('templates.publish');
        Route::post('templates/{template}/feature', [Admin\TemplateController::class, 'toggleFeatured'])->name('templates.feature');

        Route::resource('categories', Admin\TemplateCategoryController::class)->only(['index', 'store', 'update', 'destroy'])
            ->parameters(['categories' => 'category']);

        Route::get('pages', [Admin\PageController::class, 'index'])->name('pages.index');
        Route::post('pages/{page}/status', [Admin\PageController::class, 'toggleStatus'])->name('pages.status');
        Route::delete('pages/{page}', [Admin\PageController::class, 'destroy'])->name('pages.destroy');

        Route::get('domains', [Admin\DomainController::class, 'index'])->name('domains.index');
        Route::post('domains/{domain}/activate', [Admin\DomainController::class, 'activate'])->name('domains.activate');
        Route::post('domains/{domain}/verify', [Admin\DomainController::class, 'verify'])->name('domains.verify');
        Route::delete('domains/{domain}', [Admin\DomainController::class, 'destroy'])->name('domains.destroy');

        Route::get('subscriptions', [Admin\SubscriptionController::class, 'index'])->name('subscriptions.index');
        Route::put('subscriptions/{user}', [Admin\SubscriptionController::class, 'update'])->name('subscriptions.update');

        Route::get('media', [Admin\MediaController::class, 'index'])->name('media.index');
        Route::delete('media/{media}', [Admin\MediaController::class, 'destroy'])->name('media.destroy');

        Route::get('settings', [Admin\SettingController::class, 'edit'])->name('settings.edit');
        Route::put('settings', [Admin\SettingController::class, 'update'])->name('settings.update');

        Route::get('logs', [Admin\ActivityLogController::class, 'index'])->name('logs.index');
    });
});
