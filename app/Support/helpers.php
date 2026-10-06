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
