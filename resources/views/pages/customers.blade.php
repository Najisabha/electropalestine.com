@php($title = 'العملاء')
@include('layouts.admin', [
    'title' => $title,
    'slot' => view('pages.partials.customers', [
        'customers' => $customers,
        'filter' => $filter,
        'days' => $days,
        'limit' => $limit,
    ]),
])

