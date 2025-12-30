@php($title = $company->name . ' - جميع المنتجات')
@include('layouts.app', [
    'title' => $title,
    'slot' => view('store.partials.company-products-bootstrap', [
        'company' => $company,
        'products' => $products,
        'types' => $types,
        'categories' => $categories,
        'sort' => $sort,
        'minPrice' => $minPrice,
        'maxPrice' => $maxPrice,
        'minRating' => $minRating,
        'typeId' => $typeId,
        'categoryId' => $categoryId,
        'inStock' => $inStock,
        'perPage' => $perPage,
    ]),
])

