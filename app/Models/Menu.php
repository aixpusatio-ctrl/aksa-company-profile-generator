<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    /** Scroll to a section of the home page (url = section key). */
    public const TYPE_ANCHOR = 'anchor';

    /** Link to a custom page. */
    public const TYPE_PAGE = 'page';

    /** External / absolute URL. */
    public const TYPE_URL = 'url';

    /** A parent item that only groups sub menus. */
    public const TYPE_GROUP = 'group';

    public const TYPES = [
        self::TYPE_ANCHOR => 'Anchor Section',
        self::TYPE_PAGE => 'Internal Page',
        self::TYPE_URL => 'External URL',
        self::TYPE_GROUP => 'Menu Group (submenu only)',
    ];

    protected $fillable = [
        'company_profile_id', 'parent_id', 'company_page_id', 'title', 'slug', 'type', 'url',
        'open_in_new_tab', 'sort_order', 'status',
    ];

    protected function casts(): array
    {
        return ['open_in_new_tab' => 'boolean'];
    }

    public function companyProfile(): BelongsTo
    {
        return $this->belongsTo(CompanyProfile::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Menu::class, 'parent_id')->orderBy('sort_order')->orderBy('id');
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(CompanyPage::class, 'company_page_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function scopeRoots(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }
}
