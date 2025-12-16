<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Campaign;
use Illuminate\View\View;

class StoreController extends Controller
{
    public function home(): View
    {
        $categories = Category::with(['types', 'products' => fn ($q) => $q->latest()->take(6)])->get();

        $featured = Product::with(['category', 'company'])
            ->orderByDesc('created_at')
            ->take(8)
            ->get();

        // المنتجات الأكثر مبيعاً: فقط المنتجات التي تم تعليمها كـ \"ضمن المنتجات الأكثر مبيعاً\"
        $bestSelling = Product::with(['category', 'company'])
            ->where('is_best_seller', true)
            ->orderByDesc('sales_count')
            ->orderByDesc('created_at')
            ->take(12)
            ->get();

        // جميع المنتجات لعرضها في قائمة أسفل شريط الأكثر مبيعاً
        $allProducts = Product::with(['category', 'company'])
            ->orderByDesc('created_at')
            ->take(40)
            ->get();

        $campaigns = Campaign::query()
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()->toDateString());
            })
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', now()->toDateString());
            })
            ->orderByDesc('starts_at')
            ->orderByDesc('created_at')
            ->take(10)
            ->get();

        return view('store.home', compact('categories', 'featured', 'bestSelling', 'campaigns', 'allProducts'));
    }

    public function product(Product $product): View
    {
        $product->load(['category.types', 'company', 'type']);

        $related = Product::where('category_id', $product->category_id)
            ->whereKeyNot($product->getKey())
            ->take(4)
            ->get();

        return view('store.product', compact('product', 'related'));
    }

    public function category(Category $category): View
    {
        $category->load(['types', 'companies', 'products.company']);

        // جميع الأنواع التابعة لهذا الصنف
        $types = $category->types;

        // الشركات المرتبطة بالصنف عن طريق جدول الربط
        $companies = $category->companies;

        // المنتجات التابعة للصنف (نستخدمها في الشريط أو الشبكة)
        $products = $category->products()
            ->with('company')
            ->orderByDesc('created_at')
            ->get();

        return view('store.category', compact('category', 'types', 'companies', 'products'));
    }
}

