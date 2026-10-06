<?php

namespace App\Models;

use App\Support\MediaUrl;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Number;
use Illuminate\Support\Str;

class Media extends Model
{
    protected $table = 'media';

    protected $fillable = [
        'user_id', 'company_profile_id', 'collection', 'disk', 'path', 'filename', 'mime_type',
        'size', 'width', 'height', 'alt',
    ];

    protected $appends = ['url', 'human_size', 'is_image'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function companyProfile(): BelongsTo
    {
        return $this->belongsTo(CompanyProfile::class);
    }

    public function getUrlAttribute(): ?string
    {
        return MediaUrl::resolve($this->path);
    }

    public function getHumanSizeAttribute(): string
    {
        return Number::fileSize((int) $this->size, precision: 1);
    }

    public function getIsImageAttribute(): bool
    {
        return Str::startsWith((string) $this->mime_type, 'image/');
    }
}
