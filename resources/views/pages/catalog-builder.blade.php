@php($title = 'إدارة التصنيفات والمنتجات')
@include('layouts.admin', ['title' => $title, 'slot' => view('pages.partials.catalog-builder', [
    'categories' => $categories,
    'types' => $types,
    'companies' => $companies,
    'products' => $products,
])])

