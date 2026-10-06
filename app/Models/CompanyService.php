<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyService extends Model
{
    use BelongsToCompany, HasFactory;

    protected $table = 'company_services';

    protected $fillable = ['title', 'description', 'icon', 'image', 'sort_order'];
}
