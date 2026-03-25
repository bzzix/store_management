<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>{{ __('Supplier Statement') }} - {{ $supplier->name }}</title>
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
            font-size: 10px;
            line-height: 1.2;
            color: #000;
            -webkit-print-color-adjust: exact;
            box-sizing: border-box;
        }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 2mm; table-layout: fixed; }
        th, td { border: 0.1mm solid #000; padding: 2px 1px; word-break: break-word; font-size: 9px; }
        
        .header { margin-bottom: 2mm; border-bottom: 0.1mm solid #000; padding-bottom: 1mm; }
        .header h1 { font-size: 13px; margin: 0; font-weight: bold; }
        .header h2 { font-size: 10px; margin: 2px 0; font-weight: normal; }
        
        .items-table th { background: #eee; font-weight: bold; }
        
        .footer { margin-top: 2mm; font-size: 9px; border-top: 0.1mm solid #000; padding-top: 1mm; }
        
        .balance-box {
            border: 0.5mm solid #000;
            padding: 3px;
            margin-top: 2mm;
            background: #f0f0f0;
            font-weight: bold;
            font-size: 11px;
        }

        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print text-center" style="margin-bottom: 10px;">
        <button onclick="window.print()" style="padding: 5px 15px; background: #000; color: #fff; border: none; cursor: pointer; font-size: 12px;">
            {{ __('Print') }}
        </button>
    </div>

    <div class="header text-center">
        @if(get_setting('appLogo'))
            <img src="{{ get_setting('appLogo') }}" style="max-height: 55px; margin-bottom: 2px; display: block; margin: 0 auto;">
        @else
            <h1>{{ get_setting('appName', 'أولاد عبد الستار للزراعة') }}</h1>
        @endif
        <h2>إدارة / {{ get_setting('appManagerName', 'محمود حسن') }}</h2>
    </div>

    <table class="no-border">
        <tr>
            <td colspan="2" class="text-center font-bold" style="border:none; font-size: 11px;">{{ __('Supplier Statement') }}</td>
        </tr>
        <tr>
            <td style="border:none; width: 25%;" class="font-bold">المورد:</td>
            <td style="border:none;">{{ $supplier->name }}</td>
        </tr>
        <tr>
            <td style="border:none;" class="font-bold">التلفون:</td>
            <td style="border:none;">{{ $supplier->phone }}</td>
        </tr>
        <tr>
            <td style="border:none;" class="font-bold">الرصيد الافتتاحي:</td>
            <td style="border:none; font-weight:bold;">{{ number_format($supplier->opening_balance, 2) }}</td>
        </tr>
        <tr>
            <td style="border:none;" class="font-bold">الرصيد الحالي:</td>
            <td style="border:none; font-weight:bold;">{{ number_format($supplier->current_balance, 2) }}</td>
        </tr>
        <tr>
            <td style="border:none;" class="font-bold">الفترة:</td>
            <td style="border:none;">من {{ $fromDate }} إلي {{ $toDate }}</td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 25%;">المعرف / التاريخ</th>
                <th style="width: 18%;">رصيد سابق</th>
                <th style="width: 18%;">قيمة الفاتورة</th>
                <th style="width: 18%;">المدفوع (-)</th>
                <th style="width: 21%;">رصيد متبقي</th>
            </tr>
        </thead>
        <tbody>
            {{-- Previous Balance Row --}}
            <tr>
                <td class="text-center">
                    <div class="font-bold">رصيد افتتاحي</div>
                    <div style="font-size: 7px;">{{ $fromDate }}</div>
                </td>
                <td class="text-center">-</td>
                <td class="text-center">-</td>
                <td class="text-center">-</td>
                <td class="text-center font-bold">{{ number_format($transactions['previous_balance'], 2) }}</td>
            </tr>

            @foreach($items as $item)
                <tr>
                    <td class="text-center">
                        <div class="font-bold" style="font-size: 8px;">{{ $item['number'] }}</div>
                        <div style="font-size: 7px;">{{ \Carbon\Carbon::parse($item['date'])->format('m/d H:i') }}</div>
                    </td>
                    <td class="text-center">{{ number_format($item['previous_balance'], 2) }}</td>
                    <td class="text-center">{{ $item['type'] === 'purchase' ? number_format($item['value'], 2) : '-' }}</td>
                    <td class="text-center">{{ $item['deduction'] > 0 ? number_format($item['deduction'], 2) : '-' }}</td>
                    <td class="text-center font-bold">{{ number_format($item['balance'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="text-right" style="margin-top: 2mm;">
        <table style="border:none;">
            <tr style="border:none;">
                <td style="border:none; width: 60%;" class="text-left font-bold">إجمالي الإضافات:</td>
                <td style="border:none;" class="text-center">{{ number_format($transactions['total_addition'], 2) }}</td>
            </tr>
            <tr style="border:none;">
                <td style="border:none;" class="text-left font-bold">إجمالي الخصومات:</td>
                <td style="border:none;" class="text-center">{{ number_format($transactions['total_deduction'], 2) }}</td>
            </tr>
        </table>
    </div>

    <div class="balance-box text-center">
        @php
            $balanceType = $transactions['final_balance'] > 0 ? 'مستحق للمورد' : 'دائن للشركة';
        @endphp
        الرصيد النهائي ({{ $balanceType }}): {{ number_format(abs($transactions['final_balance']), 2) }}
    </div>

    <div class="footer text-center">
        <div>{{ get_setting('appAddress', 'كفر الشيخ - الرياض') }}</div>
        <div class="font-bold">{{ get_setting('appMobile', '01062226955') }}</div>
        <div style="font-size: 8px; margin-top: 2px;">طبع في: {{ now()->format('Y-m-d H:i') }}</div>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
