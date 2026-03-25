<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>{{ __('Purchase Invoice') }} #{{ $invoice->invoice_number }}</title>
    <style>
        @page {
            size: 80mm auto;
            margin: 0;
        }
        body {
            width: 72mm;
            margin: 0 auto;
            padding: 1mm 0;
            font-family: 'Arial', sans-serif;
            font-size: 10.5px;
            line-height: 1.1;
            color: #000;
            -webkit-print-color-adjust: exact;
            box-sizing: border-box;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 2mm; table-layout: fixed; }
        th, td { border: 0.1mm solid #000; padding: 3px 2px; word-break: break-word; }
        
        .header { margin-bottom: 2mm; border-bottom: 0.1mm solid #000; padding-bottom: 1mm; }
        .header h1 { font-size: 14px; margin: 0; font-weight: bold; }
        
        .items-table th { background: #f5f5f5; font-weight: bold; font-size: 10px; }
        .important-total { font-weight: bold; font-size: 11.5px; border: 0.3mm solid #000 !important; }
        
        .footer { margin-top: 2mm; font-size: 10px; border-top: 0.1mm solid #000; padding-top: 1mm; }
        
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print text-center" style="margin-bottom: 10px;">
        <button onclick="window.print()" style="padding: 5px 15px; background: #000; color: #fff; border: none; cursor: pointer;">
            {{ __('Print') }}
        </button>
    </div>

    <div class="header text-center">
        @if(get_setting('appLogo'))
            <img src="{{ get_setting('appLogo') }}" style="max-height: 60px; margin-bottom: 2px; display: block; margin: 0 auto;">
        @else
            <h1>{{ get_setting('appName', 'أولاد عبدالستار') }}</h1>
        @endif
        <h2>{{ __('Purchase Invoice') }}</h2>
    </div>

    <table>
        <tr>
            <td colspan="4" class="text-center font-bold">التاريخ : {{ $invoice->invoice_date->format('Y/m/d H:i') }}</td>
        </tr>
        <tr>
            <td colspan="4" class="text-center font-bold" style="font-size: 12px;">رقم الفاتورة : {{ $invoice->invoice_number }}</td>
        </tr>
        <tr style="font-size: 9.5px;">
            <td class="font-bold" style="width: 15%;">المورد:</td>
            <td style="width: 35%;">{{ $invoice->supplier?->name }}</td>
            <td class="font-bold" style="width: 15%;">المستلم:</td>
            <td style="width: 35%;">{{ $invoice->user?->name }}</td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 10%;">م</th>
                <th style="width: 45%;">الصنف</th>
                <th style="width: 15%;">كمية</th>
                <th style="width: 15%;">سعر</th>
                <th style="width: 15%;">إجمالي</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->product->name }}</td>
                    <td class="text-center">{{ number_format($item->quantity, 0) }}</td>
                    <td class="text-center">{{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-center">{{ number_format($item->total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table>
        <tr>
            <td class="font-bold">الإجمالي</td>
            <td class="text-center">{{ number_format($invoice->subtotal, 2) }}</td>
        </tr>
        <tr>
            <td class="font-bold">خصم</td>
            <td class="text-center">{{ number_format($invoice->discount_amount, 2) }}</td>
        </tr>
        <tr>
            <td class="important-total">الصافي</td>
            <td class="text-center important-total">{{ number_format($invoice->total_amount, 2) }}</td>
        </tr>
        <tr>
            <td class="font-bold">المدفوع</td>
            <td class="text-center">{{ number_format($invoice->paid_amount, 2) }}</td>
        </tr>
        <tr>
            <td class="important-total">المتبقي</td>
            <td class="text-center important-total">{{ number_format($invoice->total_amount - $invoice->paid_amount, 2) }}</td>
        </tr>
    </table>

    <div class="footer text-center">
        <div>{{ get_setting('appName') }} - {{ __('Warehouse Management') }}</div>
        <div class="font-bold">{{ get_setting('appMobile') }}</div>
    </div>

    <script>
        window.onload = function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('direct')) {
                window.print();
                setTimeout(() => { window.close(); }, 500);
            }
        }
    </script>
</body>
</html>
