@php($title = __('common.return_policy'))
@include('layouts.app', [
    'title' => $title,
    'slot' => view('store.partials.return-policy'),
])
