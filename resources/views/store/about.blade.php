@php($title = __('common.about_us'))
@include('layouts.app', [
    'title' => $title,
    'slot' => view('store.partials.about'),
])


