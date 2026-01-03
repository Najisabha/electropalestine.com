<?php

namespace App\Models;

use App\Http\Controllers\SitemapController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'image', 'background', 'description', 'description_en'];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function types()
    {
        return $this->belongsToMany(Type::class, 'company_type');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_company');
    }
    
    /**
     * Boot the model and register event listeners
     */
    protected static function boot(): void
    {
        parent::boot();
        
        // Clear sitemap cache when company is saved or deleted
        static::saved(function () {
            SitemapController::clearCache();
        });
        
        static::deleted(function () {
            SitemapController::clearCache();
        });
    }
}

