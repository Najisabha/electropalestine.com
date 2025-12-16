<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'budget',
        'shipping_type',
        'shipping_min_amount',
        'discount_type',
        'discount_value',
        'start_date',
        'end_date',
        'description',
        'image',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'campaign_product')
            ->withPivot(['discount_type', 'discount_value'])
            ->withTimestamps();
    }
}


