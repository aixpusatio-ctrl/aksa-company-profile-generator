<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Throwable;

class SettingService
{
    private const CACHE_KEY = 'platform.settings';

    /** @var array<string, mixed>|null */
    private ?array $settings = null;

    public const DEFAULTS = [
        'app_name' => null,
        'app_tagline' => 'Company Profile Generator',
        'app_logo' => null,
        'app_favicon' => null,
        'default_template' => 'corporate',
        'default_seo_title' => 'Buat Company Profile Profesional Dalam Hitungan Menit',
        'default_seo_description' => 'Platform SaaS untuk membuat website company profile profesional tanpa coding: pilih template, isi data, publish.',
        'default_seo_keywords' => 'company profile, website perusahaan, website builder, template company profile',
        'media_disk' => 'public',
        'max_upload_kb' => 4096,
        'registration_enabled' => '1',
        'support_email' => 'support@example.com',
    ];

    public function all(): array
    {
        if ($this->settings !== null) {
            return $this->settings;
        }

        try {
            $stored = Cache::rememberForever(self::CACHE_KEY, function () {
                if (! Schema::hasTable('settings')) {
                    return [];
                }

                return Setting::query()->pluck('value', 'key')->all();
            });
        } catch (Throwable) {
            $stored = [];
        }

        return $this->settings = array_merge(self::DEFAULTS, $stored);
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $value = $this->all()[$key] ?? null;

        return $value === null || $value === '' ? $default : $value;
    }

    public function set(array $values): void
    {
        foreach ($values as $key => $value) {
            Setting::query()->updateOrCreate(['key' => $key], ['value' => is_bool($value) ? (int) $value : $value]);
        }

        $this->flush();
    }

    public function flush(): void
    {
        Cache::forget(self::CACHE_KEY);
        $this->settings = null;
    }
}
