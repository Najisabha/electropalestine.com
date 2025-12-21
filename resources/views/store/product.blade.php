@php($title = $product->name)
@include('layouts.app', [
    'title' => $title,
    'slot' => view('store.partials.product-bootstrap', [
        'product' => $product,
        'related' => $related,
        'reviews' => $reviews ?? collect(),
    ]),
])

