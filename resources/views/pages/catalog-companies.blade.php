@php($title = 'جميع الشركات')
@include('layouts.admin', [
    'title' => $title,
    'slot' => view('pages.partials.catalog-companies', ['companies' => $companies, 'search' => $search ?? null]),
])


