@php($title = 'الأدوار و الصلاحيات')
@include('layouts.admin', ['title' => $title, 'slot' => view('pages.partials.roles', compact('roles'))])


