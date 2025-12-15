@php($title = 'تعيين كلمة مرور جديدة')
@include('layouts.app', [
    'title' => $title,
    'slot' => view('auth.partials.reset-card', ['token' => $token]),
])

