<?php

namespace App\Exports;

use App\Models\Order;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;

class ProfitByPeriodExport implements FromArray
{
    public function __construct(
        protected Carbon $from,
        protected Carbon $to,
    ) {
    }

    public function array(): array
    {
        $rows = [];

        $cursor = $this->from->copy()->startOfDay();
        while ($cursor <= $this->to) {
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

        $data = [
            ['تقرير الأرباح حسب الفترة (يومياً)'],
            ['من', $this->from->toDateTimeString()],
            ['إلى', $this->to->toDateTimeString()],
            [],
            ['التاريخ', 'عدد الطلبات', 'إجمالي المبيعات', 'الربح التقريبي'],
        ];

        foreach ($rows as $row) {
            $data[] = [
                $row['label'],
                $row['orders_count'],
                $row['sales_total'],
                $row['profit_total'],
            ];
        }

        return $data;
    }
}

