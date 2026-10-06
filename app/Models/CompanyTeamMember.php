<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyTeamMember extends Model
{
    use BelongsToCompany, HasFactory;

    protected $table = 'company_team';

    protected $fillable = ['name', 'position', 'photo', 'bio', 'linkedin', 'email', 'sort_order'];
}
