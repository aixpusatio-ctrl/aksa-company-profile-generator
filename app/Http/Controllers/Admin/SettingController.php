<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Template;
use App\Services\MediaService;
use App\Services\SettingService;
use App\Support\Activity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function __construct(private readonly SettingService $settings) {}

    public function edit(): View
    {
        return view('admin.settings.edit', [
            'settings' => $this->settings->all(),
            'templates' => Template::query()->published()->orderBy('name')->get(['name', 'slug']),
            'disks' => array_keys(config('filesystems.disks')),
            'system' => [
                'Laravel' => app()->version(),
                'PHP' => PHP_VERSION,
                'Environment' => app()->environment(),
                'Database' => config('database.default'),
                'Queue' => config('queue.default'),
                'Cache' => config('cache.default'),
                'Platform domain' => config('platform.domain'),
                'Central domains' => implode(', ', config('platform.central_domains')),
                'Domain verifier' => config('platform.custom_domains.verifier'),
            ],
        ]);
    }

    public function update(Request $request, MediaService $media): RedirectResponse
    {
        $data = $request->validate([
            'app_name' => ['required', 'string', 'max:60'],
            'app_tagline' => ['nullable', 'string', 'max:120'],
            'support_email' => ['nullable', 'email'],
            'default_template' => ['nullable', Rule::exists('templates', 'slug')],
            'default_seo_title' => ['nullable', 'string', 'max:120'],
            'default_seo_description' => ['nullable', 'string', 'max:500'],
            'default_seo_keywords' => ['nullable', 'string', 'max:255'],
            'media_disk' => ['required', Rule::in(array_keys(config('filesystems.disks')))],
            'max_upload_kb' => ['required', 'integer', 'min:256', 'max:20480'],
            'registration_enabled' => ['nullable', 'boolean'],
            'app_logo' => ['nullable', MediaService::imageRule()],
            'app_favicon' => ['nullable', MediaService::imageRule()],
            'app_logo_media' => ['nullable', 'string', 'max:255'],
            'app_favicon_media' => ['nullable', 'string', 'max:255'],
        ]);
        unset($data['app_logo_media'], $data['app_favicon_media']);

        foreach (['app_logo', 'app_favicon'] as $field) {
            $data[$field] = $media->resolveImageInput($request->all(), $field, $request->user(), null, setting($field), 'logos');
        }

        $data['registration_enabled'] = $request->boolean('registration_enabled') ? '1' : '0';

        $this->settings->set($data);
        Activity::log('admin.settings', 'Pengaturan aplikasi diperbarui');

        return back()->with('success', 'Pengaturan disimpan.');
    }
}
