<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Company;
use App\Models\Product;
use App\Models\Type;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(): View
    {
        return view('dashboard.index', [
            'metrics' => [
                'categories' => Category::count(),
                'types' => Type::count(),
                'companies' => Company::count(),
                'products' => Product::count(),
            ],
            'latestProducts' => Product::with(['category', 'type', 'company'])
                ->latest()
                ->take(5)
                ->get(),
        ]);
    }
}

