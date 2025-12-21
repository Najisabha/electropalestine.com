<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>تقرير المبيعات حسب التاريخ</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h1 { font-size: 20px; color: #10b981; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { border: 1px solid #e5e7eb; padding: 6px 8px; text-align: center; }
        th { background-color: #10b981; color: #111827; }
    </style>
</head>
<body>
    <h1>تقرير المبيعات حسب التاريخ</h1>
    <p>من: {{ $from->format('Y-m-d') }} &nbsp;&nbsp; إلى: {{ $to->format('Y-m-d') }}</p>

    <table>
        <thead>
        <tr>
            <th>عدد الطلبات</th>
            <th>إجمالي المبيعات</th>
            <th>إجمالي الربح التقريبي</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>{{ $orders_count }}</td>
            <td>${{ number_format($sales_total, 2) }}</td>
            <td>${{ number_format($profit_total, 2) }}</td>
        </tr>
        </tbody>
    </table>

    <p style="margin-top: 24px; font-size: 11px; color: #6b7280;">
        ملاحظة: الربح التقريبي يعتمد على تكلفة المنتج الحالية، وقد يختلف عن الربح المحاسبي الدقيق للفترات الماضية.
    </p>
</body>
</html>

