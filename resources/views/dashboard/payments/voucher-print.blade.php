<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>{{ __('Voucher') }} - {{ $payment->payment_number }}</title>
    <style>
        @page { size: 80mm auto; margin: 0; }
        body {
            width: 72mm; margin: 0 auto; padding: 2mm 0;
            font-family: 'Arial', sans-serif; font-size: 11px; line-height: 1.3; color: #000;
        }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .header { margin-bottom: 3mm; border-bottom: 0.2mm solid #000; padding-bottom: 2mm; }
        .header h1 { font-size: 15px; margin: 0; }
        .voucher-title { font-size: 14px; text-decoration: underline; margin: 2mm 0; }
        .info-table { width: 100%; border-collapse: collapse; margin: 3mm 0; }
        .info-table td { padding: 1.5mm 0; vertical-align: top; }
        .amount-box {
            border: 0.5mm solid #000; padding: 3mm; margin: 4mm 0;
            background: #f9f9f9; font-size: 14px; font-weight: bold;
        }
        .footer { margin-top: 5mm; border-top: 0.2mm solid #000; padding-top: 2mm; font-size: 10px; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="no-print text-center" style="margin-bottom: 5mm;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #000; color: #fff; border: none; cursor: pointer;">
            {{ __('Print') }}
        </button>
    </div>

    <div class="header text-center">
        @if(get_setting('appLogo'))
            <img src="{{ get_setting('appLogo') }}" style="max-height: 60px; margin-bottom: 2px;">
        @else
            <h1>{{ get_setting('appName', 'أولاد عبد الستار') }}</h1>
        @endif
        <div class="voucher-title font-bold">
            {{ $payment->voucher_type === 'receipt' ? 'سند قبض نقدية' : 'سند صرف نقدية' }}
        </div>
    </div>

    <table class="info-table">
        <tr>
            <td width="30%" class="font-bold">رقم السند:</td>
            <td>{{ $payment->payment_number }}</td>
        </tr>
        <tr>
            <td class="font-bold">التاريخ:</td>
            <td>{{ $payment->payment_date->format('Y-m-d') }}</td>
        </tr>
        <tr>
            <td class="font-bold">{{ $payment->voucher_type === 'receipt' ? 'استلمنا من:' : 'صرفنا إلى:' }}</td>
            <td>{{ $payment->payer->name }}</td>
        </tr>
        <tr>
            <td class="font-bold">طريقة الدفع:</td>
            <td>{{ __($payment->payment_method) }}</td>
        </tr>
        @if($payment->reference_number)
        <tr>
            <td class="font-bold">رقم المرجع:</td>
            <td>{{ $payment->reference_number }}</td>
        </tr>
        @endif
    </table>

    <div class="amount-box text-center">
        مبلغ وقدره: {{ number_format($payment->amount, 2) }} ج.م
    </div>

    @if($payment->notes)
    <div style="margin: 3mm 0;">
        <span class="font-bold">وذلك عن:</span>
        <p style="margin: 1mm 0; white-space: pre-wrap;">{{ $payment->notes }}</p>
    </div>
    @endif

    <div style="margin-top: 8mm; display: flex; justify-content: space-between;">
        <div class="text-center" style="width: 45%;">
            <div class="font-bold">المستلم</div>
            <div style="margin-top: 10mm; border-top: 0.1mm solid #000;"></div>
        </div>
        <div class="text-center" style="width: 45%;">
            <div class="font-bold">المحاسب</div>
            <div style="margin-top: 10mm; border-top: 0.1mm solid #000;"></div>
        </div>
    </div>

    <div class="footer text-center">
        <div>{{ get_setting('appAddress') }}</div>
        <div class="font-bold">{{ get_setting('appMobile') }}</div>
        <div style="font-size: 8px; margin-top: 2mm;">طبع بواسطة: {{ $payment->user->name }} | {{ now()->format('Y-m-d H:i') }}</div>
    </div>

    <script>window.onload = function() { window.print(); }</script>
</body>
</html>
