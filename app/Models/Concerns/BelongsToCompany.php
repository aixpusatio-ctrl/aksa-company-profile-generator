<?php

namespace App\Models\Concerns;

use App\Models\CompanyProfile;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Shared behaviour for sortable content that belongs to a company profile
 * (services, products, projects, team, testimonials, gallery, pages).
 */
trait BelongsToCompany
{
    use HasMediaUrls;

    public static function bootBelongsToCompany(): void
    {
        static::creating(function ($model) {
            if ($model->sort_order === null || $model->sort_order === 0) {
                $max = static::query()
                    ->where('company_profile_id', $model->company_profile_id)
                    ->max('sort_order');
                $model->sort_order = ((int) $max) + 1;
            }
        });
    }

    public function companyProfile(): BelongsTo
    {
        return $this->belongsTo(CompanyProfile::class);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
