@php($title = 'قائمة الرغبات')
@include('layouts.app', [
    'title' => $title,
    'slot' => view('store.partials.favorites-bootstrap', [
        'favoriteProducts' => $favoriteProducts ?? collect(),
    ]),
])
