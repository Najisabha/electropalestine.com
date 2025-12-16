@php
    $locale = app()->getLocale();
    $isRTL = $locale === 'ar';
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRTL ? 'rtl' : 'ltr' }}">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فاتورة #{{ $order->id }}</title>
    <style>
        /* الأنماط الأساسية والخطوط */
        body {
            font-family: 'DejaVu Sans', sans-serif;
            text-align: right;
            direction: rtl;
            unicode-bidi: embed;
            color: #333;
            font-size: 14px;
        }
        * {
            font-family: 'DejaVu Sans', sans-serif;
            direction: rtl;
            unicode-bidi: embed;
        }
        p, span, div, td, th {
            letter-spacing: normal;
            word-spacing: normal;
        }

        /* تنسيق الأرقام والإنجليزية (LTR) */
        [dir="ltr"], .ltr {
            direction: ltr;
            unicode-bidi: embed;
            text-align: left;
        }
        
        /* الحاوية الرئيسية للفاتورة */
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #ddd; /* إطار خفيف */
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.05); /* ظل خفيف وأكثر احترافية */
            line-height: 24px;
        }
        
        /* جدول الرأس (معلومات الشركة والفاتورة) */
        .header-table {
            width: 100%;
            margin-bottom: 25px;
            border-bottom: 3px solid #0db777; /* خط فاصل بلون الشركة */
            padding-bottom: 10px;
        }
        .header-table td {
            padding: 5px 0;
            vertical-align: top;
        }
        .header-table tr td:last-child {
            text-align: left; /* معلومات الفاتورة على اليسار */
        }
        .company-info h1 {
            margin: 0;
            font-size: 28px;
            font-weight: bold;
        }
        
        /* قسم العميل/الشحن */
        .client-info-box {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid #eee; /* إطار خفيف بدلاً من خلفية رمادية صريحة */
            border-radius: 6px;
        }
        .client-info-box h3 {
            margin-top: 0;
            margin-bottom: 10px;
            font-size: 16px;
            color: #0db777;
            border-bottom: 2px solid #0db777;
            padding-bottom: 5px;
            display: inline-block;
        }

        /* جدول المنتجات */
        .items-table {
            width: 100%;
            line-height: inherit;
            text-align: right;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .items-table th {
            background: #0db777;
            color: white;
            padding: 12px 10px;
            font-weight: bold;
            text-align: right; /* ضمان أن تكون العناوين على اليمين */
        }
        .items-table td {
            padding: 10px;
            border: 1px solid #f0f0f0; /* حدود خفيفة بين الصفوف */
        }
        
        /* تنسيق الأرقام في الجدول (الكمية، سعر الوحدة، المجموع) */
        .items-table td:nth-child(2), /* الكمية */
        .items-table td:nth-child(3), /* سعر الوحدة */
        .items-table td:nth-child(4) { /* المجموع */
            direction: ltr; 
            text-align: right; /* الأرقام تكون على اليمين لسهولة المقارنة */
        }
        
        /* صف المجموع الكلي */
        .items-table tr.total td {
            border-top: 3px solid #0db777;
            background-color: #f7fffb; /* خلفية فاتحة جدًا */
            padding: 15px 10px;
        }
        .items-table tr.total td:first-child { 
            text-align: right; 
            font-weight: bold;
        }
        .items-table tr.total td:last-child { 
            font-size: 20px; 
            color: #0db777; 
            font-weight: bold;
        }
        
        /* التذييل */
        .footer {
            text-align: center; 
            font-size: 12px; 
            color: #777; 
            border-top: 1px solid #eee; 
            padding-top: 15px;
        }

        /* الألوان العامة */
        .text-green { color: #0db777; }
        .text-warning { color: #ffc107; } /* لون افتراضي للحالة قيد الانتظار */
        .tag { /* تنسيق جميل للحالة */
            padding: 5px 10px; 
            border-radius: 4px; 
            font-size: 12px; 
            font-weight: bold;
            display: inline-block;
        }
        .tag-confirmed { background-color: #e6fff4; color: #0db777; }
        .tag-pending { background-color: #fff8e6; color: #ffc107; }
    </style>
</head>
<body>

    <div class="invoice-box">
        
        <table class="header-table">
            <tr>
                <td class="company-info" style="width: 50%;">
                    <h1 class="text-green">electropalestine</h1>
                    <p style="margin: 5px 0 0 0; font-size: 12px; color: #777;">{{ __('common.simplified_tax_invoice') }}</p>
                    <p style="margin: 5px 0 0 0;">
                        <span style="font-weight: bold;">{{ __('common.address') }}:</span> [{{ __('common.address') }}]
                    </p>
                </td>
                <td style="width: 50%; text-align: left;">
                    <strong style="color: #555;">{{ __('common.invoice_number') }}:</strong> <span class="ltr" style="font-weight: bold; font-size: 16px;">#{{ $order->id }}</span><br>
                    <strong style="color: #555;">{{ __('common.issue_date') }}:</strong> <span class="ltr">{{ $order->created_at->format('Y-m-d') }}</span><br>
                    <strong style="color: #555;">{{ __('common.status') }}:</strong> 
                    <span class="tag {{ $order->status == 'confirmed' ? 'tag-confirmed' : 'tag-pending' }}">
                        {{ $order->status === 'confirmed' ? __('common.confirmed') : __('common.pending') }}
                    </span>
                </td>
            </tr>
        </table>
        
        <div class="card shadow-sm border-0 mb-4" dir="{{ $isRTL ? 'rtl' : 'ltr' }}">
            <div class="card-header bg-light border-bottom-0">
                <h5 class="mb-0 text-success fw-bold">
                    <i class="bi bi-person-badge-fill me-2"></i> {{ __('common.customer_info') }}
                </h5>
            </div>
            <div class="card-body pt-2">
                <div class="row">
                    <div class="col-md-6 col-sm-12 mb-3 mb-md-0 border-end pe-4">
                        <div class="d-flex justify-content-between align-items-baseline mb-1">
                            <strong class="text-secondary">{{ __('common.name') }}:</strong>
                            <span class="ltr text-dark">{{ $user->first_name }} {{ $user->last_name }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-baseline">
                            <strong class="text-secondary">{{ __('common.address') }}:</strong>
                            <span class="text-dark">{{ $order->shipping_address ?? __('common.not_available') }}</span>
                        </div>
                    </div>

                    <div class="col-md-6 col-sm-12 ps-4">
                        <div class="d-flex justify-content-between align-items-baseline mb-1">
                            <strong class="text-secondary">{{ __('common.phone_number') }}:</strong>
                            <span class="ltr text-dark">{{ $user->whatsapp_prefix }}{{ $user->phone }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-baseline">
                            <strong class="text-secondary">{{ __('common.email') }}:</strong>
                            <span class="ltr text-dark">{{ $user->email }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 50%;">{{ __('common.product') }}</th>
                    <th style="width: 15%;">{{ __('common.quantity') }}</th>
                    <th style="width: 15%;">{{ __('common.unit_price') }}</th>
                    <th style="width: 20%;">{{ __('common.total') }}</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $order->product_name }}</td>
                    <td><span class="ltr">{{ $order->quantity }}</span></td>
                    <td><span class="ltr">${{ number_format($order->unit_price, 2) }}</span></td>
                    <td><span class="ltr">${{ number_format($order->total, 2) }}</span></td>
                </tr>
                <tr class="total">
                    <td colspan="3" style="text-align: left; font-size: 16px;">
                        <strong>{{ __('common.total_required') }}:</strong>
                    </td>
                    <td style="text-align: right;">
                        <span class="ltr"><strong>${{ number_format($order->total, 2) }}</strong></span>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="footer">
            <p style="margin: 0;">{{ __('common.invoice_footer') }}</p>
            <p style="margin: 5px 0 0 0;">{{ __('common.invoice_contact') }} <span dir="ltr">info@electropalestine.com</span></p>
        </div>
    </div>

</body>
</html>