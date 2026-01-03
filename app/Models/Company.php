<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'image'];

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
}

