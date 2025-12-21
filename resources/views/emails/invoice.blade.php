<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فاتورة طلبية #{{ $order->id }}</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; background: #f4f4f4; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
        <div style="text-align: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 3px solid #0db777;">
            <h1 style="color: #0db777; margin: 0;">electropalestine</h1>
            <p style="color: #666; margin: 10px 0 0 0;">فاتورة طلبية</p>
        </div>

        <div style="margin-bottom: 20px;">
            <h2 style="color: #0db777; font-size: 20px; margin-bottom: 15px;">مرحباً {{ $user->first_name }} {{ $user->last_name }},</h2>
            <p style="color: #333; font-size: 16px;">
                تم تأكيد طلبيتك بنجاح! نرفق لك الفاتورة المرفقة في هذا البريد الإلكتروني.
            </p>
        </div>

        <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
            <h3 style="color: #0db777; margin-top: 0;">تفاصيل الطلبية</h3>
            <p><strong>رقم الطلبية:</strong> #{{ $order->id }}</p>
            <p><strong>تاريخ الطلب:</strong> {{ $order->created_at->format('Y/m/d H:i:s') }}</p>
            <p><strong>طريقة الدفع:</strong>
                @if($order->payment_method === 'balance_points')
                    الرصيد/النقاط
                @elseif($order->payment_method === 'cash_on_delivery')
                    دفع عند الاستلام
                @elseif($order->payment_method === 'visa_mastercard')
                    بطاقة ائتمانية (فيزا/ماستر كارد)
                @else
                    أخرى
                @endif
            </p>

            @php($items = is_array($order->items ?? null) ? $order->items : [])

            @if(!empty($items))
                <table style="width: 100%; border-collapse: collapse; margin-top: 15px;">
                    <thead>
                        <tr style="background: #0db777; color: #fff;">
                            <th style="padding: 8px; text-align: right;">المنتج</th>
                            <th style="padding: 8px; text-align: center;">الكمية</th>
                            <th style="padding: 8px; text-align: right;">سعر الوحدة</th>
                            <th style="padding: 8px; text-align: right;">الإجمالي</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $item)
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="padding: 8px;">{{ $item['name'] ?? '' }}</td>
                                <td style="padding: 8px; text-align: center;">{{ $item['quantity'] ?? 0 }}</td>
                                <td style="padding: 8px; text-align: right;">${{ number_format($item['unit_price'] ?? 0, 2) }}</td>
                                <td style="padding: 8px; text-align: right;">${{ number_format($item['total'] ?? 0, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p><strong>المنتج:</strong> {{ $order->product_name }}</p>
                <p><strong>الكمية:</strong> {{ $order->quantity }}</p>
                <p><strong>سعر الوحدة:</strong> ${{ number_format($order->unit_price, 2) }}</p>
            @endif

            <p style="margin-top: 10px;">
                <strong>المجموع الكلي:</strong>
                <span style="color: #0db777; font-size: 18px; font-weight: bold;">
                    ${{ number_format($order->total, 2) }}
                </span>
            </p>
        </div>

        <div style="margin-top: 30px; padding-top: 20px; border-top: 2px solid #0db777; text-align: center; color: #666; font-size: 12px;">
            <p>شكراً لاختيارك electropalestine</p>
            <p>تم إرفاق الفاتورة بصيغة PDF مع هذا البريد الإلكتروني</p>
        </div>
    </div>
</body>
</html>
