@php($title = __('common.track_order'))
@include('layouts.app', [
    'title' => $title,
    'slot' => view('store.partials.track-order'),
])
