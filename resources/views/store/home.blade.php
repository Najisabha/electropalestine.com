@php($title = 'متجر إلكتروني أخضر')
@include('layouts.app', ['title' => $title, 'slot' => view('store.partials.home-bootstrap', ['categories' => $categories, 'featured' => $featured])])

