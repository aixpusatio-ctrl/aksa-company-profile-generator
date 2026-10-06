<?php

use App\Services\SettingService;

if (! function_exists('setting')) {
    /**
     * Read a global platform setting (managed from Admin → Settings).
     */
    function setting(string $key, mixed $default = null): mixed
    {
        return app(SettingService::class)->get($key, $default);
    }
}

if (! function_exists('app_name')) {
    function app_name(): string
    {
        return (string) setting('app_name', config('app.name'));
    }
}

if (! function_exists('central_url')) {
    /**
     * Absolute URL on the central application host (safe to call while a
     * tenant website request is being handled).
     */
    function central_url(string $path = '/'): string
    {
        return rtrim((string) config('app.url'), '/').'/'.ltrim($path, '/');
    }
}
