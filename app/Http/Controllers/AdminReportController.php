<?php

namespace App\Http\Controllers;

use App\Exports\SalesByCategoryExport;
use App\Exports\SalesByDateExport;
use App\Models\Category;
use App\Models\Company;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminReportController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (! auth()->check() || strtolower(auth()->user()->role) !== 'admin') {
                abort(403);
            }

            return $next($request);
        });
    }

    public function index(Request $request): View
    {
        // تبويب 1: المبيعات حسب التاريخ
        $from = $request->query('from')
            ? Carbon::parse($request->query('from'))->startOfDay()
            : Carbon::now()->startOfMonth();
        $to = $request->query('to')
            ? Carbon::parse($request->query('to'))->endOfDay()
            : Carbon::now()->endOfDay();

        $dateQuery = Order::confirmed()
            ->whereBetween('created_at', [$from, $to]);

        $salesByDate = [
            'orders_count' => (clone $dateQuery)->count(),
            'sales_total' => (clone $dateQuery)->sum('total'),
            'profit_total' => Order::approximateProfitBetween($from, $to),
        ];

        // تبويب 2: المبيعات حسب الصنف / الشركة
        $categoryId = $request->query('category_id');
        $companyId = $request->query('company_id');

        $categorySales = null;
        if ($categoryId || $companyId) {
            $categorySales = SalesByCategoryExport::buildQuery($categoryId, $companyId)->get();
        }

        // تبويب 3: الأرباح حسب الفترة (يومياً ضمن النطاق)
        $profitByPeriod = $this->buildProfitByPeriod($from, $to);

        return view('pages.reports', [
            'from' => $from,
            'to' => $to,
            'salesByDate' => $salesByDate,
            'categories' => Category::orderBy('name')->get(),
            'companies' => Company::orderBy('name')->get(),
            'categoryId' => $categoryId,
            'companyId' => $companyId,
            'categorySales' => $categorySales,
            'profitPeriods' => $profitByPeriod['rows'],
            'profitSummary' => $profitByPeriod['summary'],
        ]);
    }

    public function exportSalesByDateExcel(Request $request)
    {
        $from = $request->query('from')
            ? Carbon::parse($request->query('from'))->startOfDay()
            : Carbon::now()->startOfMonth();
        $to = $request->query('to')
            ? Carbon::parse($request->query('to'))->endOfDay()
            : Carbon::now()->endOfDay();

        $export = new SalesByDateExport($from, $to);

        return Excel::download($export, 'sales_by_date_'.$from->format('Ymd').'_to_'.$to->format('Ymd').'.xlsx');
    }

    public function exportSalesByDatePdf(Request $request)
    {
        $from = $request->query('from')
            ? Carbon::parse($request->query('from'))->startOfDay()
            : Carbon::now()->startOfMonth();
        $to = $request->query('to')
            ? Carbon::parse($request->query('to'))->endOfDay()
            : Carbon::now()->endOfDay();

        $query = Order::confirmed()
            ->whereBetween('created_at', [$from, $to]);

        $data = [
            'from' => $from,
            'to' => $to,
            'orders_count' => (clone $query)->count(),
            'sales_total' => (clone $query)->sum('total'),
            'profit_total' => Order::approximateProfitBetween($from, $to),
        ];

        $pdf = Pdf::loadView('reports.sales-by-date-pdf', $data);

        return $pdf->download('sales_by_date_'.$from->format('Ymd').'_to_'.$to->format('Ymd').'.pdf');
    }

    public function exportSalesByCategoryExcel(Request $request)
    {
        $categoryId = $request->query('category_id');
        $companyId = $request->query('company_id');

        $export = new SalesByCategoryExport($categoryId, $companyId);

        return Excel::download($export, 'sales_by_category.xlsx');
    }

    public function exportProfitByPeriodExcel(Request $request)
    {
        $from = $request->query('from')
            ? Carbon::parse($request->query('from'))->startOfDay()
            : Carbon::now()->startOfMonth();
        $to = $request->query('to')
            ? Carbon::parse($request->query('to'))->endOfDay()
            : Carbon::now()->endOfDay();

        $export = new \App\Exports\ProfitByPeriodExport($from, $to);

        return Excel::download($export, 'profit_by_period_'.$from->format('Ymd').'_to_'.$to->format('Ymd').'.xlsx');
    }

    public function exportProfitByPeriodPdf(Request $request)
    {
        $from = $request->query('from')
            ? Carbon::parse($request->query('from'))->startOfDay()
            : Carbon::now()->startOfMonth();
        $to = $request->query('to')
            ? Carbon::parse($request->query('to'))->endOfDay()
            : Carbon::now()->endOfDay();

        $profitByPeriod = $this->buildProfitByPeriod($from, $to);

        $pdf = Pdf::loadView('reports.profit-by-period-pdf', [
            'from' => $from,
            'to' => $to,
            'rows' => $profitByPeriod['rows'],
            'summary' => $profitByPeriod['summary'],
        ]);

        return $pdf->download('profit_by_period_'.$from->format('Ymd').'_to_'.$to->format('Ymd').'.pdf');
    }

    /**
     * حساب الأرباح اليومية ضمن فترة معينة لاستخدامها في التبويب الثالث والتقارير.
     */
    protected function buildProfitByPeriod(Carbon $from, Carbon $to): array
    {
        $rows = [];

        $cursor = $from->copy()->startOfDay();
        while ($cursor <= $to) {
            $start = $cursor->copy()->startOfDay();
            $end = $cursor->copy()->endOfDay();

            $query = Order::confirmed()
                ->whereBetween('created_at', [$start, $end]);

            $ordersCount = (clone $query)->count();
            $salesTotal = (clone $query)->sum('total');
            $profitTotal = Order::approximateProfitBetween($start, $end);

            $rows[] = [
                'label' => $start->format('Y-m-d'),
                'orders_count' => $ordersCount,
                'sales_total' => $salesTotal,
                'profit_total' => $profitTotal,
            ];

            $cursor->addDay();
        }

        $collection = collect($rows);

        /** @var Collection<int, array> $nonEmpty */
        $nonEmpty = $collection->filter(fn ($row) => $row['orders_count'] > 0 || $row['sales_total'] != 0 || $row['profit_total'] != 0);

        $totalProfit = $collection->sum('profit_total');

        return [
            'rows' => $rows,
            'summary' => [
                'total_profit' => $totalProfit,
                'best_period' => $nonEmpty->sortByDesc('profit_total')->first(),
                'worst_period' => $nonEmpty->sortBy('profit_total')->first(),
            ],
        ];
    }
}

