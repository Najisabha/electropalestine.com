<?php

namespace App\Exports;

use App\Models\Order;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;

class SalesByDateExport implements FromArray
{
    public function __construct(
        protected Carbon $from,
        protected Carbon $to,
    ) {
    }

    public function array(): array
    {
        $query = Order::confirmed()
            ->whereBetween('created_at', [$this->from, $this->to]);

        $ordersCount = (clone $query)->count();
        $salesTotal = (clone $query)->sum('total');
        $profitTotal = Order::approximateProfitBetween($this->from, $this->to);

        return [
            ['تقرير المبيعات حسب التاريخ'],
            ['من', $this->from->toDateTimeString()],
            ['إلى', $this->to->toDateTimeString()],
            [],
            ['عدد الطلبات', 'إجمالي المبيعات', 'إجمالي الربح التقريبي'],
            [$ordersCount, $salesTotal, $profitTotal],
        ];
    }
}

