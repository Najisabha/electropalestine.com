@php($title = 'إضافة حملة إعلانية')
@include('layouts.admin', [
    'title' => $title,
    'slot' => view('pages.partials.add-campaign', [
        'products' => $products ?? collect(),
        'categories' => $categories ?? collect(),
        'types' => $types ?? collect(),
        'companies' => $companies ?? collect(),
    ]),
])

