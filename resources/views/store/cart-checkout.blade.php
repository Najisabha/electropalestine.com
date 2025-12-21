@php($title = __('common.checkout_title'))
@include('layouts.app', [
    'title' => $title,
    'slot' => view('store.partials.checkout-cart-bootstrap', [
        'items' => $items,
        'total' => $total,
        'userBalance' => $userBalance ?? 0,
        'userPoints' => $userPoints ?? 0,
    ]),
])

