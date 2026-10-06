<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageView extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = ['company_profile_id', 'path', 'referrer', 'visitor_hash', 'viewed_on'];

    protected function casts(): array
    {
        return ['viewed_on' => 'date'];
    }

    public function companyProfile(): BelongsTo
    {
        return $this->belongsTo(CompanyProfile::class);
    }
}
