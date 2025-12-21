@php($title = 'التقارير')
@include('layouts.admin', [
    'title' => $title,
    'slot' => view('pages.partials.reports', [
        'from' => $from,
        'to' => $to,
        'salesByDate' => $salesByDate,
        'categories' => $categories,
        'companies' => $companies,
        'categoryId' => $categoryId,
        'companyId' => $companyId,
        'categorySales' => $categorySales,
        'profitPeriods' => $profitPeriods,
        'profitSummary' => $profitSummary,
    ]),
])

