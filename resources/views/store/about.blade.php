@php($title = 'من نحن')
@include('layouts.app', [
    'title' => $title,
    'slot' => view('store.partials.about'),
])


