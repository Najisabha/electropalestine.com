@php($title = 'إظهار الطلبات')
@include('layouts.admin', ['title' => $title, 'slot' => view('pages.partials.orders', ['orders' => $orders])])

