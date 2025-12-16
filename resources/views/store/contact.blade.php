@php($title = __('common.contact_us'))
@include('layouts.app', [
    'title' => $title,
    'slot' => view('store.partials.contact'),
])


