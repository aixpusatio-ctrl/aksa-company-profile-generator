<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaUrl
{
    /**
     * Resolve a stored media path (or absolute URL) to a public URL.
     */
    public static function resolve(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://', '//', 'data:image/'])) {
            return $path;
        }

        return Storage::disk(config('platform.media.disk'))->url($path);
    }
}
