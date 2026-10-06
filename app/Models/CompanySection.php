<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanySection extends Model
{
    protected $fillable = ['company_profile_id', 'key', 'title', 'subtitle', 'is_enabled', 'sort_order'];

    protected function casts(): array
    {
        return ['is_enabled' => 'boolean'];
    }

    public function companyProfile(): BelongsTo
    {
        return $this->belongsTo(CompanyProfile::class);
    }

    public function label(): string
    {
        return config("website-templates.sections.{$this->key}", ucfirst($this->key));
    }
}
