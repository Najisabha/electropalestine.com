<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تقرير الأرباح حسب الفترة</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 16px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 6px 8px;
            text-align: center;
        }
        th {
            background-color: #f0f0f0;
        }
    </style>
</head>
<body>
    <h2>تقرير الأرباح حسب الفترة (يومياً)</h2>
    <p>
        من: {{ $from->toDateString() }}<br>
        إلى: {{ $to->toDateString() }}
    </p>

    @if($summary['best_period'] || $summary['worst_period'])
        <p>
            <strong>إجمالي الربح التقريبي:</strong>
            {{ number_format($summary['total_profit'] ?? 0, 2) }}
        </p>
    @endif

    <table>
        <thead>
        <tr>
            <th>التاريخ</th>
            <th>عدد الطلبات</th>
            <th>إجمالي المبيعات</th>
            <th>الربح التقريبي</th>
        </tr>
        </thead>
        <tbody>
        @foreach($rows as $row)
            <tr>
                <td>{{ $row['label'] }}</td>
                <td>{{ $row['orders_count'] }}</td>
                <td>{{ number_format($row['sales_total'], 2) }}</td>
                <td>{{ number_format($row['profit_total'], 2) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</body>
</html>

