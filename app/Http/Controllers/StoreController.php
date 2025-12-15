<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
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

        return view('store.home', compact('categories', 'featured'));
    }

    public function product(Product $product): View
    {
        $product->load(['category', 'company', 'type']);

        $related = Product::where('category_id', $product->category_id)
            ->whereKeyNot($product->getKey())
            ->take(4)
            ->get();

        return view('store.product', compact('product', 'related'));
    }
}

