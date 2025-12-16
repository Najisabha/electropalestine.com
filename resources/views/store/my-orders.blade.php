@php($title = 'طلبياتي')
@include('layouts.app', [
    'title' => $title,
    'slot' => view('store.partials.my-orders-bootstrap', [
        'orders' => $orders ?? [],
    ]),
])
