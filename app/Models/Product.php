<?php

namespace App\Models;

use App\Http\Controllers\SitemapController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'type_id',
        'company_id',
        'name',
        'name_en',
        'slug',
        'cost_price',
        'price',
        'stock',
        'thumbnail',
        'is_best_seller',
        'is_active',
        'description',
        'description_en',
        'image',
        'sales_count',
        'rating_average',
        'rating_count',
        'points_reward',
        'role_prices',
    ];

    protected $casts = [
        'role_prices' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * صافي الربح للوحدة = سعر البيع - سعر التكلفة (تقريبي).
     */
    public function getProfitPerUnitAttribute(): ?float
    {
        if ($this->cost_price === null || $this->price === null) {
            return null;
        }

        return (float) ($this->price - $this->cost_price);
    }

    /**
     * هامش الربح بالنسبة المئوية.
     */
    public function getProfitMarginAttribute(): ?float
    {
        if ($this->cost_price === null || $this->cost_price == 0) {
            return null;
        }

        return (float) (($this->profit_per_unit / $this->cost_price) * 100);
    }

    /**
     * نطاق لأعلى المنتجات ربحية.
     */
    public function scopeMostProfitable(Builder $query, int $limit = 10): Builder
    {
        return $query
            ->whereNotNull('cost_price')
            ->whereNotNull('price')
            ->select('*')
            ->orderByRaw('(price - cost_price) DESC')
            ->limit($limit);
    }

    /**
     * المنتجات الأقل حركة حسب sales_count.
     */
    public function scopeLowMovement(Builder $query, int $limit = 10): Builder
    {
        return $query
            ->orderBy('sales_count', 'asc')
            ->limit($limit);
    }

    /**
     * نطاق للمنتجات المفعّلة فقط (ظاهرة في المتجر).
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * نطاق محسن للمنتجات مع eager loading للعلاقات الأساسية
     */
    public function scopeWithRelations(Builder $query): Builder
    {
        return $query->with([
            'category:id,name,slug',
            'company:id,name',
            'type:id,name,slug,category_id',
        ]);
    }

    /**
     * نطاق للمنتجات المميزة (الأكثر مبيعاً) مع ترتيب محسن
     */
    public function scopeBestSelling(Builder $query, int $limit = 12): Builder
    {
        return $query
            ->where('is_best_seller', true)
            ->active()
            ->orderByDesc('sales_count')
            ->orderByDesc('created_at')
            ->limit($limit);
    }

    /**
     * نطاق للمنتجات الجديدة مع ترتيب محسن
     */
    public function scopeNewest(Builder $query, int $limit = 20): Builder
    {
        return $query
            ->active()
            ->orderByDesc('created_at')
            ->limit($limit);
    }

    /**
     * نطاق للمنتجات الأعلى تقييماً
     */
    public function scopeTopRated(Builder $query, int $limit = 20): Builder
    {
        return $query
            ->active()
            ->where('rating_count', '>', 0)
            ->orderByDesc('rating_average')
            ->orderByDesc('rating_count')
            ->limit($limit);
    }

    /**
     * المنتجات منخفضة المخزون.
     */
    public function scopeLowStock(Builder $query, ?int $threshold = null): Builder
    {
        $threshold = $threshold ?? (int) config('catalog.low_stock_threshold', 10);

        return $query->where('stock', '<=', $threshold);
    }

    public function getTranslatedNameAttribute(): string
    {
        $locale = app()->getLocale();
        if ($locale === 'en' && $this->name_en) {
            return $this->name_en;
        }

        return $this->name ?? '';
    }

    public function getTranslatedDescriptionAttribute(): ?string
    {
        $locale = app()->getLocale();
        if ($locale === 'en' && $this->description_en) {
            return $this->description_en;
        }

        return $this->description;
    }

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
    
    /**
     * Boot the model and register event listeners
     */
    protected static function boot(): void
    {
        parent::boot();
        
        // Clear sitemap cache when product is saved or deleted
        static::saved(function (Product $product) {
            SitemapController::clearCache();
            // مسح cache الصفحة الرئيسية
            \Illuminate\Support\Facades\Cache::forget('store.home.ar');
            \Illuminate\Support\Facades\Cache::forget('store.home.en');
            // مسح cache المنتجات المشابهة
            \Illuminate\Support\Facades\Cache::forget('product.related.' . $product->category_id);
            // مسح cache التقييمات للمنتج
            \Illuminate\Support\Facades\Cache::forget('product.reviews.' . $product->id);
        });
        
        static::deleted(function (Product $product) {
            SitemapController::clearCache();
            \Illuminate\Support\Facades\Cache::forget('store.home.ar');
            \Illuminate\Support\Facades\Cache::forget('store.home.en');
            \Illuminate\Support\Facades\Cache::forget('product.related.' . $product->category_id);
            \Illuminate\Support\Facades\Cache::forget('product.reviews.' . $product->id);
        });
    }
}
