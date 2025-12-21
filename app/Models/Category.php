<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'name_en', 'slug', 'description', 'description_en', 'image'];

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

    public function types(): HasMany
    {
        return $this->hasMany(Type::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function companies()
    {
        return $this->belongsToMany(Company::class, 'category_company');
    }
}
