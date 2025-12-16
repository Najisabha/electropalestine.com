@php($title = __('common.our_story'))
@include('layouts.app', [
    'title' => $title,
    'slot' => view('store.partials.story'),
])


