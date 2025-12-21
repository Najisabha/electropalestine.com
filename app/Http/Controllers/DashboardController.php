<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Company;
use App\Models\Order;
use App\Models\Product;
use App\Models\Type;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->check() || strtolower(auth()->user()->role) !== 'admin') {
                abort(403);
            }
            return $next($request);
        });
    }

    public function index(): View
    {
        // إحصائيات المبيعات والربح (مع كاش بسيط لتخفيف الضغط)
        $salesStats = Cache::remember('dashboard.sales_stats', 300, function () {
            return [
                'today' => [
                    'sales' => Order::salesToday(),
                    'profit' => Order::approximateProfitToday(),
                ],
                'week' => [
                    'sales' => Order::salesThisWeek(),
                    'profit' => Order::approximateProfitThisWeek(),
                ],
                'month' => [
                    'sales' => Order::salesThisMonth(),
                    'profit' => Order::approximateProfitThisMonth(),
                ],
                'status_counts' => [
                    'pending' => Order::countByStatus('pending'),
                    'confirmed' => Order::countByStatus('confirmed'),
                    'cancelled' => Order::countByStatus('cancelled'),
                ],
            ];
        });

        $lowStockProducts = Product::with(['category', 'company'])
            ->lowStock()
            ->orderBy('stock', 'asc')
            ->take(10)
            ->get();

        $topSellingProducts = Product::with(['category', 'company'])
            ->orderByDesc('sales_count')
            ->take(5)
            ->get();

        $lowMovementProducts = Product::with(['category', 'company'])
            ->lowMovement(5)
            ->get();

        $topCustomers = User::topCustomers(5);

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
            'salesStats' => $salesStats,
            'lowStockProducts' => $lowStockProducts,
            'topSellingProducts' => $topSellingProducts,
            'lowMovementProducts' => $lowMovementProducts,
            'topCustomers' => $topCustomers,
        ]);
    }
}

