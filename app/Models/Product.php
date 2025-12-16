<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'type_id',
        'company_id',
        'name',
        'slug',
        'cost_price',
        'price',
        'stock',
        'thumbnail',
        'is_best_seller',
        'description',
        'image',
        'sales_count',
        'rating_average',
        'rating_count',
        'points_reward',
        'role_prices',
    ];

    protected $casts = [
        'role_prices' => 'array',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(Type::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function campaigns(): BelongsToMany
    {
        return $this->belongsToMany(Campaign::class, 'campaign_product')
            ->withPivot(['discount_type', 'discount_value'])
            ->withTimestamps();
    }
}
