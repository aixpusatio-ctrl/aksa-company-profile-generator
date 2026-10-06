<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TemplateCategory extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'sort_order'];

    public function templates(): HasMany
    {
        return $this->hasMany(Template::class);
    }
}
