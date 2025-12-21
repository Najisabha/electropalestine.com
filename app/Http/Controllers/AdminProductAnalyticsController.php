<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Company;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminProductAnalyticsController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (! auth()->check() || strtolower(auth()->user()->role) !== 'admin') {
                abort(403);
            }

            return $next($request);
        });
    }

    public function index(Request $request): View
    {
        $categoryId = $request->query('category_id');
        $companyId = $request->query('company_id');
        $minPrice = $request->query('min_price');
        $maxPrice = $request->query('max_price');
        $minMargin = $request->query('min_margin');
        $maxMargin = $request->query('max_margin');
        $onlyNegative = $request->boolean('only_negative');

        $query = Product::with(['category', 'company'])
            ->orderByDesc('sales_count');

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        if ($minPrice !== null && $minPrice !== '') {
            $query->where('price', '>=', (float) $minPrice);
        }

        if ($maxPrice !== null && $maxPrice !== '') {
            $query->where('price', '<=', (float) $maxPrice);
        }

        // نصفي في الذاكرة على هامش الربح لأنه يعتمد على Accessor
        $products = $query->get()->map(function (Product $product) {
            $unitsSold = $product->sales_count ?? 0;
            $profitPerUnit = $product->profit_per_unit;
            $totalProfit = $profitPerUnit !== null ? $profitPerUnit * $unitsSold : null;

            $product->units_sold = $unitsSold;
            $product->total_profit = $totalProfit;

            return $product;
        });

        if ($minMargin !== null && $minMargin !== '') {
            $products = $products->filter(function (Product $product) use ($minMargin) {
                return $product->profit_margin !== null && $product->profit_margin >= (float) $minMargin;
            });
        }

        if ($maxMargin !== null && $maxMargin !== '') {
            $products = $products->filter(function (Product $product) use ($maxMargin) {
                return $product->profit_margin !== null && $product->profit_margin <= (float) $maxMargin;
            });
        }

        if ($onlyNegative) {
            $products = $products->filter(function (Product $product) {
                return $product->profit_per_unit !== null && $product->profit_per_unit < 0;
            });
        }

        $categories = Category::orderBy('name')->get();
        $companies = Company::orderBy('name')->get();

        return view('pages.products-analytics', [
            'products' => $products,
            'categories' => $categories,
            'companies' => $companies,
            'filters' => [
                'category_id' => $categoryId,
                'company_id' => $companyId,
                'min_price' => $minPrice,
                'max_price' => $maxPrice,
                'min_margin' => $minMargin,
                'max_margin' => $maxMargin,
                'only_negative' => $onlyNegative,
            ],
        ]);
    }
}

