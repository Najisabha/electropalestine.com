@php($title = 'إنشاء حساب جديد')
@include('layouts.app', [
    'title' => $title,
    'slot' => view('auth.partials.register-card'),
])

