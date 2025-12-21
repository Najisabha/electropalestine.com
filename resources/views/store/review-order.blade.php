@php($title = 'تقييم الطلبية')
@include('layouts.app', [
    'title' => $title,
    'slot' => view('store.review-order-content', [
        'order' => $order,
    ]),
])

