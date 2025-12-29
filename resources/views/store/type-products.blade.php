@php($title = $type->translated_name . ' - ' . ($category->translated_name ?? ''))
@include('layouts.app', [
    'title' => $title,
    'slot' => view('store.partials.type-products-bootstrap', [
        'type' => $type,
        'category' => $category,
        'products' => $products,
        'companies' => $companies,
        'sort' => $sort,
        'minPrice' => $minPrice,
        'maxPrice' => $maxPrice,
        'minRating' => $minRating,
        'companyId' => $companyId,
        'inStock' => $inStock,
        'perPage' => $perPage,
    ]),
])
