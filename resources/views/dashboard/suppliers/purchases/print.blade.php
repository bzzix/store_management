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
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 2mm; table-layout: fixed; }
        th, td { border: 0.1mm solid #000; padding: 3px 2px; word-break: break-word; }
        
        .no-border td { border: none !important; }
        .header { margin-bottom: 2mm; border-bottom: 0.1mm solid #000; padding-bottom: 1mm; }
        .header h1 { font-size: 14px; margin: 0; font-weight: bold; }
        .header h2 { font-size: 11px; margin: 2px 0; font-weight: normal; }
        
        .items-table th { background: #f5f5f5; font-weight: bold; font-size: 10px; }
        
        .totals-grid td { font-weight: normal; }
        .totals-grid .label-bold { font-weight: bold; }
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
        <h2>إدارة / {{ get_setting('appManagerName', 'محمود حسن') }}</h2>
    </div>

    {{-- Info Table --}}
    <table>
        <tr>
            <td colspan="4" class="text-center font-bold" style="font-size: 11px;">التاريخ : {{ $invoice->invoice_date->format('Y/m/d H:i') }}</td>
        </tr>
        <tr>
            <td colspan="4" class="text-center font-bold" style="font-size: 13px;">فاتورة مشتريات رقم : {{ $invoice->invoice_number }}</td>
        </tr>
        <tr style="font-size: 9.5px;">
            <td class="label-bold" style="width: 13%;">المورد:</td>
            <td style="width: 37%;">{{ $invoice->supplier?->name }}</td>
            <td class="label-bold" style="width: 13%;">المستخدم:</td>
            <td style="width: 37%;">{{ $invoice->user?->name }}</td>
        </tr>
        <tr style="font-size: 9.5px;">
            <td class="label-bold">التليفون:</td>
            <td>{{ $invoice->supplier?->phone }}</td>
            <td class="label-bold">المخزن:</td>
            <td>{{ $invoice->warehouse?->name ?? '---' }}</td>
        </tr>
        <tr style="font-size: 9.5px;">
            <td class="label-bold">العنوان:</td>
            <td colspan="3">{{ $invoice->supplier?->address ?? '---' }}</td>
        </tr>
    </table>

    {{-- Items Table --}}
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 6%;">م</th>
                <th style="width: 50%;">الصنف</th>
                <th style="width: 10%;">كمية</th>
                <th style="width: 17%;">السعر</th>
                <th style="width: 17%;">المبلغ</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->is_custom ? $item->custom_name : ($item->product->name ?? 'منتج غير موجود') }}</td>
                    <td class="text-center">{{ number_format($item->quantity, 0) }}</td>
                    <td class="text-center">{{ number_format($item->unit_price, 0) }}</td>
                    <td class="text-center">{{ number_format($item->total, 0) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Totals Grid --}}
    <table class="totals-grid">
        <tr>
            <td style="width: 40%; font-size: 9.5px;">عدد العبوات : <span class="font-bold">{{ number_format($invoice->items->sum('quantity'), 0) }}</span></td>
            <td class="label-bold" style="width: 25%;">البضاعة</td>
            <td class="text-center">{{ number_format($invoice->subtotal, 0) }}</td>
        </tr>
        <tr>
            <td style="font-size: 9.5px;">كمية الوزن : <span class="font-bold">{{ number_format($invoice->items->sum('quantity_in_base_unit'), 0) }}</span></td>
            <td class="label-bold">رصيد سابق</td>
            <td class="text-center font-bold">{{ number_format($invoice->previous_balance, 0) }}</td>
        </tr>
        <tr>
            <td rowspan="5" class="text-center" style="vertical-align: middle; font-size: 9px; border: 1px solid #000;">
                <div class="label-bold">الملاحظات:</div>
                {{ $invoice->notes ?? '---' }}
            </td>
            <td class="label-bold">إجمالي</td>
            <td class="text-center font-bold">{{ number_format($invoice->subtotal + $invoice->previous_balance, 0) }}</td>
        </tr>
        <tr>
            <td>خصم + ضريبة</td>
            <td class="text-center">{{ number_format($invoice->discount_amount - $invoice->tax_amount, 0) }}</td>
        </tr>
        <tr>
            <td class="important-total">رصيد نهائي</td>
            <td class="text-center important-total">{{ number_format($invoice->total_amount, 0) }}</td>
        </tr>
        <tr>
            <td class="label-bold">المدفوع</td>
            <td class="text-center font-bold">{{ number_format($invoice->paid_amount, 0) }}</td>
        </tr>
        <tr>
            <td class="important-total">المتبقي</td>
            <td class="text-center important-total">{{ number_format($invoice->total_amount - $invoice->paid_amount, 0) }}</td>
        </tr>
    </table>

    <div class="footer text-center">
        <div>{{ get_setting('appAddress', 'كفر الشيخ - الرياض - أبوشريف - طريق الحامول مصنع السكر') }}</div>
        <div class="font-bold">{{ get_setting('appMobile', '01062226955 - 01029666024') }} - {{ get_setting('appPhone', '0473896884') }}</div>
    </div>

    <script>
        window.onload = function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('direct')) {
                window.print();
                setTimeout(() => {
                    window.close();
                }, 500);
            }
        }
    </script>
</body>
</html>
