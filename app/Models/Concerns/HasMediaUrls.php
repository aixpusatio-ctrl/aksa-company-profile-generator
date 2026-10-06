<?php

namespace App\Models\Concerns;

use App\Support\MediaUrl;

trait HasMediaUrls
{
    /**
     * Public URL for an image/file attribute, e.g. $service->url('image').
     */
    public function url(string $attribute): ?string
    {
        return MediaUrl::resolve($this->getAttribute($attribute));
    }
}
