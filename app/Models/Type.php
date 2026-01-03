<?php

namespace App\Models;

use App\Http\Controllers\SitemapController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Type extends Model
{
    use HasFactory;

    protected $fillable = ['category_id', 'name', 'name_en', 'slug', 'image'];

    public function getTranslatedNameAttribute(): string
    {
        $locale = app()->getLocale();
        if ($locale === 'en' && $this->name_en) {
            return $this->name_en;
        }

        return $this->name ?? '';
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function companies()
    {
        return $this->belongsToMany(Company::class, 'company_type');
    }
    
    /**
     * Boot the model and register event listeners
     */
    protected static function boot(): void
    {
        parent::boot();
        
        // Clear sitemap cache when type is saved or deleted
        static::saved(function () {
            SitemapController::clearCache();
        });
        
        static::deleted(function () {
            SitemapController::clearCache();
        });
    }
}
