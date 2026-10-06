<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyProject extends Model
{
    use BelongsToCompany, HasFactory;

    protected $table = 'company_projects';

    protected $fillable = ['title', 'description', 'image', 'client', 'location', 'year', 'category', 'url', 'sort_order'];
}
