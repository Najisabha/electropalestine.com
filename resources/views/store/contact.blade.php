@php($title = 'تواصل معنا')
@include('layouts.app', [
    'title' => $title,
    'slot' => view('store.partials.contact'),
])


