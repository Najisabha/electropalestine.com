@php($title = 'تسجيل الدخول')
@include('layouts.app', [
    'title' => $title,
    'slot' => view('auth.partials.login-card'),
])
