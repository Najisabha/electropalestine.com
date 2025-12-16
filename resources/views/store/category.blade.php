@php($title = $category->name . ' – الأصناف والأنواع المرتبطة')
@include('layouts.app', [
    'title' => $title,
    'slot' => view('store.partials.category-bootstrap', [
        'category' => $category,
        'types' => $types,
        'companies' => $companies,
        'products' => $products,
    ]),
])

