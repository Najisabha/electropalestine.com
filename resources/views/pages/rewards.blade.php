@php($title = 'إدارة المكافآت')
@include('layouts.admin', [
    'title' => $title,
    'slot' => view('pages.partials.rewards', [
        'rewards' => $rewards ?? collect(),
        'categories' => $categories ?? collect(),
        'types' => $types ?? collect(),
        'products' => $products ?? collect(),
        'typesForJs' => $typesForJs ?? collect(),
        'productsForJs' => $productsForJs ?? collect(),
    ]),
])

