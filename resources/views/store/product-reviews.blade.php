@php($title = __('common.customer_ratings') . ' - ' . $product->name)
@include('layouts.app', [
    'title' => $title,
    'slot' => view('store.partials.product-reviews-bootstrap', [
        'product' => $product,
        'reviews' => $reviews,
        'ratingCount' => $ratingCount,
        'ratingAverage' => $ratingAverage,
    ]),
])
