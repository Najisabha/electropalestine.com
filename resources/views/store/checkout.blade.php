@php($title = 'إتمام الشراء')
@include('layouts.app', [
    'title' => $title,
    'slot' => view('store.partials.checkout-bootstrap', [
        'product' => $product ?? null,
        'quantity' => $quantity ?? 1,
        'total' => $total ?? 0,
        'userBalance' => $userBalance ?? 0,
        'userPoints' => $userPoints ?? 0,
    ]),
])
