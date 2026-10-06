<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyGalleryItem extends Model
{
    use BelongsToCompany, HasFactory;

    protected $table = 'company_gallery';

    protected $fillable = ['image', 'title', 'description', 'category', 'sort_order'];
}
