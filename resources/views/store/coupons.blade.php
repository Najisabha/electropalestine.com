@php($title = 'كوبوناتي')
@include('layouts.app', [
    'title' => $title,
    'slot' => view('store.partials.coupons-bootstrap', [
        'userCoupons' => $userCoupons ?? collect(),
    ]),
])
