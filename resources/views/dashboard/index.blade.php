@php($title = 'لوحة التحكم')
@include('layouts.admin', [
    'title' => $title,
    'slot' => view('dashboard.partials.content-bootstrap', [
        'metrics' => $metrics,
        'latestProducts' => $latestProducts,
        'salesStats' => $salesStats,
        'lowStockProducts' => $lowStockProducts,
        'topSellingProducts' => $topSellingProducts,
        'lowMovementProducts' => $lowMovementProducts,
        'topCustomers' => $topCustomers,
    ])
])

