@php($title = 'إعدادات الحساب الشخصي')
@include('layouts.app', [
    'title' => $title,
    'slot' => view('store.partials.account-settings-bootstrap', [
        'user' => $user ?? null,
    ]),
])
