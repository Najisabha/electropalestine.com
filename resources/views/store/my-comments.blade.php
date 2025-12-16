@php($title = 'تعليقاتي')
@include('layouts.app', [
    'title' => $title,
    'slot' => view('store.partials.my-comments-bootstrap', [
        'comments' => $comments ?? [],
    ]),
])
