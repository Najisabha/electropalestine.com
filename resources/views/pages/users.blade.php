@php($title = 'إظهار المستخدمين')
@include('layouts.admin', [
    'title' => $title,
    'slot' => view('pages.partials.users', ['users' => $users, 'roles' => $roles ?? collect()]),
])

