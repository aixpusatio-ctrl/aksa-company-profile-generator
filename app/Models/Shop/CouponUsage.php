<?php

namespace App\Models\Shop;

use Illuminate\Database\Eloquent\Model;

class CouponUsage extends Model
{
    protected $fillable = ['coupon_id', 'order_id', 'customer_id', 'email', 'discount'];
}
