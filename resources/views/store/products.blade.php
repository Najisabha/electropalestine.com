@php($title = __('common.all_products'))
@include('layouts.app', [
    'title' => $title,
    'slot' => view('store.partials.products-bootstrap', [
        'products' => $products,
        'categories' => $categories,
        'types' => $types,
        'companies' => $companies,
        'search' => $search,
        'categoryId' => $categoryId,
        'typeId' => $typeId,
        'companyId' => $companyId,
        'minPrice' => $minPrice,
        'maxPrice' => $maxPrice,
        'minRating' => $minRating,
        'inStock' => $inStock,
        'featured' => $featured,
        'sort' => $sort,
        'perPage' => $perPage,
    ]),
])
