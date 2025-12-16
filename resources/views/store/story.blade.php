@php($title = 'قصتنا')
@include('layouts.app', [
    'title' => $title,
    'slot' => view('store.partials.story'),
])


