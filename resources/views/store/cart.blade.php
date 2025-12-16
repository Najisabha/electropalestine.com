@php($title = 'سلة الشراء')
@include('layouts.app', [
    'title' => $title,
    'slot' => view('store.partials.cart-bootstrap', [
        'cartItems' => $cartItems ?? [],
        'total' => $total ?? 0,
    ]),
])
