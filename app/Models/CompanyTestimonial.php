<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyTestimonial extends Model
{
    use BelongsToCompany, HasFactory;

    protected $table = 'company_testimonials';

    protected $fillable = ['customer_name', 'company', 'photo', 'testimonial', 'rating', 'sort_order'];

    protected function casts(): array
    {
        return ['rating' => 'integer'];
    }
}
