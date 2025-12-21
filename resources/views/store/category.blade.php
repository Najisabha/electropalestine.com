@php($title = $category->translated_name . ' – الأصناف والأنواع المرتبطة')
@include('layouts.app', [
    'title' => $title,
    'slot' => view('store.partials.category-bootstrap', [
        'category' => $category,
        'types' => $types,
        'companies' => $companies,
        'products' => $products,
    ]),
])

