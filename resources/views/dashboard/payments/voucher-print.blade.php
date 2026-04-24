<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>{{ __('Voucher') }} #{{ $payment->payment_number }}</title>
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
    @php
        $isCustomer = $payment->payer_type === \App\Models\Customer::class;
        $isRefund = ($isCustomer && $payment->voucher_type === 'disbursement') || 
                    (!$isCustomer && $payment->voucher_type === 'receipt');

        $currentBalance = $payment->payer ? (float)$payment->payer->current_balance : 0;
        
        if ($isRefund) {
            $previousBalance = $currentBalance - (float)$payment->amount;
        } else {
            $previousBalance = $currentBalance + (float)$payment->amount;
        }
    @endphp

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
            <td colspan="4" class="text-center font-bold" style="font-size: 11px;">التاريخ : {{ $payment->payment_date->format('Y/m/d H:i') }}</td>
        </tr>
        <tr>
            <td colspan="4" class="text-center font-bold" style="font-size: 13px;">
                {{ $payment->voucher_type === 'receipt' ? 'سند قبض نقدية رقم' : 'سند صرف نقدية رقم' }} : {{ $payment->payment_number }}
            </td>
        </tr>
        <tr style="font-size: 9.5px;">
            <td class="label-bold" style="width: 13%;">الاسم:</td>
            <td style="width: 37%;">{{ $payment->payer?->name ?? '---' }}</td>
            <td class="label-bold" style="width: 13%;">المستخدم:</td>
            <td style="width: 37%;">{{ $payment->user?->name ?? '---' }}</td>
        </tr>
        <tr style="font-size: 9.5px;">
            <td class="label-bold">الدفع:</td>
            <td>{{ __($payment->payment_method) }}</td>
            <td class="label-bold">المرجع:</td>
            <td>{{ $payment->reference_number ?? '---' }}</td>
        </tr>
    </table>

    {{-- Description Table --}}
    @if($payment->notes)
    <table>
        <tr>
            <td class="label-bold" style="width: 20%; background: #f5f5f5;">البيان:</td>
            <td>{{ $payment->notes }}</td>
        </tr>
    </table>
    @endif

    {{-- Totals Grid --}}
    <table class="totals-grid">
        <tr>
            <td rowspan="3" class="text-center" style="vertical-align: middle; font-size: 9px; border: 1px solid #000; width: 40%;">
                <div class="label-bold" style="margin-bottom: 5px;">التوقيع</div>
                <br><br><br>
                -------------------
            </td>
            <td class="label-bold" style="width: 30%;">الرصيد السابق</td>
            <td class="text-center font-bold" style="width: 30%;">{{ number_format($previousBalance, 2) }}</td>
        </tr>
        <tr>
            <td class="label-bold">المدفوع</td>
            <td class="text-center font-bold">{{ number_format($payment->amount, 2) }}</td>
        </tr>
        <tr>
            <td class="important-total">المتبقي</td>
            <td class="text-center important-total">{{ number_format($currentBalance, 2) }}</td>
        </tr>
    </table>

    <div class="footer text-center">
        <div>{{ get_setting('appAddress', 'كفر الشيخ - الرياض - أبوشريف - طريق الحامول مصنع السكر') }}</div>
        <div class="font-bold">{{ get_setting('appMobile', '01062226955 - 01029666024') }} - {{ get_setting('appPhone', '0473896884') }}</div>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
