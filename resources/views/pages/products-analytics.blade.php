@php($title = 'تحليلات المنتجات')
@include('layouts.admin', [
    'title' => $title,
    'slot' => view('pages.partials.products-analytics', [
        'products' => $products,
        'categories' => $categories,
        'companies' => $companies,
        'filters' => $filters,
    ]),
])

