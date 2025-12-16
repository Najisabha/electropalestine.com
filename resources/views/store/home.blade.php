@php($title = __('common.green_electronics_store'))
@include('layouts.app', [
    'title' => $title,
    'slot' => view('store.partials.home-bootstrap', [
        'categories' => $categories,
        'featured' => $featured,
        'bestSelling' => $bestSelling ?? collect(),
        'campaigns' => $campaigns ?? collect(),
        'allProducts' => $allProducts ?? collect(),
    ]),
])

