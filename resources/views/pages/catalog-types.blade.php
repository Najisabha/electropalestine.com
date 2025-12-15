@php($title = 'جميع الأنواع')
@include('layouts.admin', [
    'title' => $title,
    'slot' => view('pages.partials.catalog-types', ['types' => $types, 'search' => $search ?? null]),
])


