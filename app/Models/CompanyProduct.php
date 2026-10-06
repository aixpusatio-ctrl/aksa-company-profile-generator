<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyProduct extends Model
{
    use BelongsToCompany, HasFactory;

    protected $table = 'company_products';

    protected $fillable = ['name', 'description', 'image', 'price', 'category', 'sort_order'];

    protected function casts(): array
    {
        return ['price' => 'decimal:2'];
    }

    public function formattedPrice(): ?string
    {
        return $this->price === null ? null : 'Rp '.number_format((float) $this->price, 0, ',', '.');
    }
}
