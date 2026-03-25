<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>{{ __('Invoice') }} - {{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: 'xbriyaz', 'DejaVu Sans', Arial, sans-serif;
            color: #333;
            margin: 0;
            padding: 20px;
            direction: rtl;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #ddd;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            color: #004ba0;
        }
        .invoice-details {
            width: 100%;
            margin-bottom: 30px;
            border-collapse: collapse;
        }
        .invoice-details td {
            padding: 8px;
            vertical-align: top;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .table th, .table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }
        .table th {
            background-color: #f8f9fa;
            color: #333;
            font-weight: bold;
        }
        .totals {
            width: 40%;
            margin-right: auto;
            margin-left: 0;
            border-collapse: collapse;
        }
        .totals th, .totals td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .totals th {
            background-color: #f8f9fa;
            width: 60%;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #777;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>فاتورة مشتريات</h1>
        <p>{{ get_setting('appName', 'أولاد عبد الستار للأعلاف') }}</p>
    </div>

    <table class="invoice-details">
        <tr>
            <td>
                <strong>رقم الفاتورة:</strong> <span style="direction:ltr; display:inline-block;">{{ $invoice->invoice_number }}</span><br>
                <strong>التاريخ:</strong> {{ $invoice->invoice_date->format('Y-m-d') }}<br>
                <strong>الحالة:</strong> {{ __($invoice->status) }}
            </td>
            <td>
                <strong>المورد:</strong> {{ $invoice->supplier->name }}<br>
                <strong>المخزن:</strong> {{ $invoice->warehouse->name }}<br>
                <strong>الدفع:</strong> {{ __($invoice->payment_status) }}
            </td>
        </tr>
    </table>

    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>المنتج</th>
                <th>العبوات (الكمية)</th>
                <th>الوزن الإجمالي</th>
                <th>سعر الوحدة</th>
                <th>الضريبة</th>
                <th>الخصم</th>
                <th>الإجمالي</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalPackages = 0;
                $totalWeight = 0;
            @endphp
            @foreach($invoice->items as $index => $item)
            @php
                $qty = (float)$item->quantity;
                $weight = $qty * ($item->product->weight ?? 1); // Assuming weight per unit exists, default to 1
                $totalPackages += $qty;
                $totalWeight += $weight;
            @endphp
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->product->name }}</td>
                <td>{{ $qty }}</td>
                <td>{{ number_format($weight, 2) }} كجم</td>
                <td>{{ number_format($item->unit_price, 2) }}</td>
                <td>{{ number_format($item->tax_amount, 2) }}</td>
                <td>{{ number_format($item->discount_amount, 2) }}</td>
                <td>{{ number_format($item->total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #f8f9fa; font-weight: bold;">
                <td colspan="2" style="text-align: left;">الإجمالي:</td>
                <td>{{ number_format($totalPackages, 2) }} عبوة</td>
                <td>{{ number_format($totalWeight, 2) }} كجم</td>
                <td colspan="4"></td>
            </tr>
        </tfoot>
    </table>

    <table class="totals">
        <tr>
            <th>الإجمالي الفرعي</th>
            <td>{{ number_format($invoice->subtotal, 2) }}</td>
        </tr>
        <tr>
            <th>الضريبة الكلية</th>
            <td>{{ number_format($invoice->tax_amount, 2) }}</td>
        </tr>
        <tr>
            <th>الخصم الكلي</th>
            <td>{{ number_format($invoice->discount_amount, 2) }}</td>
        </tr>
        <tr style="border-top: 2px solid #ccc;">
            <th>إجمالي الفاتورة</th>
            <td><strong>{{ number_format($invoice->subtotal + $invoice->tax_amount - $invoice->discount_amount, 2) }}</strong></td>
        </tr>
        <tr>
            <th>رصيد المورد السابق</th>
            <td>{{ number_format($invoice->previous_balance, 2) }}</td>
        </tr>
        <tr style="background-color: #e9ecef;">
            <th>إجمالي الحساب المطلوب</th>
            <td><strong>{{ number_format($invoice->total_amount, 2) }}</strong></td>
        </tr>
        <tr>
            <th>المدفوع حالياً</th>
            <td>{{ number_format($invoice->paid_amount, 2) }}</td>
        </tr>
        <tr style="background-color: #d4edda; color: #155724; border-top: 2px solid #c3e6cb;">
            <th>الرصيد المتبقي (النهائي)</th>
            <td><strong>{{ number_format($invoice->total_amount - $invoice->paid_amount, 2) }}</strong></td>
        </tr>
    </table>

    @if($invoice->notes)
    <div style="margin-top: 30px;">
        <strong>ملاحظات:</strong>
        <p>{{ $invoice->notes }}</p>
    </div>
    @endif

    <div class="footer">
        تم الإنشاء بواسطة {{ get_setting('appName', 'أولاد عبد الستار') }} - {{ \Carbon\Carbon::now()->format('Y-m-d H:i') }}
    </div>

</body>
</html>
