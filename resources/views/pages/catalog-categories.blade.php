@php($title = 'جميع الأصناف الرئيسية')
@include('layouts.admin', [
    'title' => $title,
    'slot' => view('pages.partials.catalog-categories', ['categories' => $categories, 'search' => $search ?? null]),
])


