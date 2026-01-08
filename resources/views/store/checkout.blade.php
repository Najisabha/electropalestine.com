@php($title = 'إتمام الشراء')
@include('layouts.app', [
    'title' => $title,
    'slot' => view('store.partials.checkout-bootstrap', compact('product', 'quantity', 'total', 'userBalance', 'userPoints')),
])

@include('store.partials.address-modal')
