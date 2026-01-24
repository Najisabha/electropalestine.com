@php($title = 'التحقق من رقم الهاتف')
@include('layouts.app', [
    'title' => $title,
    'slot' => view('auth.partials.verify-phone-card', ['phone' => $phone]),
])
