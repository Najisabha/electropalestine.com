<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Reward extends Model
{
    protected $fillable = [
        'title',
        'description',
        'points_required',
        'type',
        'product_id',
        'value',
        'stock',
        'image',
        'is_active',
        'coupon_code',
    ];

    protected $casts = [
        'title' => 'array',
        'description' => 'array',
        'is_active' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function userRewards(): HasMany
    {
        return $this->hasMany(UserReward::class);
    }

    public function getTitleTranslatedAttribute(): string
    {
        $locale = app()->getLocale() ?? 'ar';
        $title = $this->title ?? [];

        if (is_array($title) && isset($title[$locale])) {
            return $title[$locale];
        }

        return is_array($title) ? (reset($title) ?: '') : (string) $title;
    }

    public function getDescriptionTranslatedAttribute(): string
    {
        $locale = app()->getLocale() ?? 'ar';
        $description = $this->description ?? [];

        if (is_array($description) && isset($description[$locale])) {
            return $description[$locale];
        }

        return is_array($description) ? (reset($description) ?: '') : (string) $description;
    }
}

