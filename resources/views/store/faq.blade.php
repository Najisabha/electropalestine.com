@php($title = __('common.faq'))
@include('layouts.app', [
    'title' => $title,
    'slot' => view('store.partials.faq'),
])
