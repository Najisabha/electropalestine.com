<?php

namespace App\Exports;

use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;

class SalesByCategoryExport implements FromArray
{
    public function __construct(
        protected ?int $categoryId = null,
        protected ?int $companyId = null,
    ) {
    }

    public static function buildQuery(?int $categoryId = null, ?int $companyId = null)
    {
        $query = Order::confirmed()
            ->join('products', 'products.name', '=', 'orders.product_name')
            ->leftJoin('categories', 'categories.id', '=', 'products.category_id')
            ->leftJoin('companies', 'companies.id', '=', 'products.company_id')
            ->select([
                DB::raw('COALESCE(categories.name, "غير محدد") as category_name'),
                DB::raw('COALESCE(companies.name, "غير محدد") as company_name'),
                DB::raw('COUNT(orders.id) as orders_count'),
                DB::raw('SUM(orders.total) as sales_total'),
            ])
            ->groupBy('category_name', 'company_name');

        if ($categoryId) {
            $query->where('products.category_id', $categoryId);
        }

        if ($companyId) {
            $query->where('products.company_id', $companyId);
        }

        return $query;
    }

    public function array(): array
    {
        $rows = static::buildQuery($this->categoryId, $this->companyId)->get();

        $data = [
            ['المبيعات حسب الصنف / الشركة'],
            ['الصنف', 'الشركة', 'عدد الطلبات', 'إجمالي المبيعات'],
        ];

        foreach ($rows as $row) {
            $data[] = [
                $row->category_name,
                $row->company_name,
                $row->orders_count,
                $row->sales_total,
            ];
        }

        return $data;
    }
}

