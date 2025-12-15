@php($title = 'استعادة كلمة المرور')
@include('layouts.app', [
    'title' => $title,
    'slot' => view('auth.partials.forgot-card'),
])

