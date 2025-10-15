
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فاتورة ضريبية مبسطة</title>
    <style>
        @page { size: 80mm auto; margin: 0; }
        body { font-family: 'Tajawal', sans-serif; width: 80mm; margin: 0 auto; font-size: 12px; line-height: 1.6; text-align: center; }
        .invoice { padding: 6mm 4mm; }
        h3 { margin: 4px 0; font-size: 15px; }
        .muted { color: #555; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin: 6px 0; }
        th, td { padding: 4px; border-bottom: 1px dashed #000; font-size: 12px; text-align: center; vertical-align: top; }
        thead th { border-bottom: 1px solid #000; font-weight: bold; }
        .totals td { border: none; font-size: 12px; padding: 3px; text-align: left; }
        .totals tr:last-child td { font-weight: bold; border-top: 1px solid #000; padding-top: 6px; text-align: center; }
        .qr { text-align: center; margin-top: 8px; }
        .footer { margin-top: 8px; font-size: 11px; text-align: center; }
    </style>
</head>
<body>
<div class="invoice">

    <div class="center">
        <img src="{{ asset('assets/imgs/logo/logo.png') }}" alt="شعار" style="max-width:80px;margin-bottom:4px">
        <h3>شركة قمر سمايا للتزيين النسائي</h3>
        <div class="muted">المملكة العربية السعودية – الرياض</div>
        <div>الرقم الضريبي: 311127360700003</div>
    </div>

    <div style="margin-top:6px; text-align:right; font-size:12px;">
        <div>رقم الفاتورة: {{ $order->id }}</div>
        <div>تاريخ الفاتورة: {{ $order->created_at->format('Y-m-d H:i:s') }}</div>
        <div>تمت الطباعة بواسطة: <strong>{{ auth()->user()->name ?? 'غير محدد' }}</strong></div>
        <div>وقت الطباعة: {{ now()->format('Y-m-d H:i:s') }}</div>
    </div>

    @php
        $vatPercent = 15;
        $totalWithVat = $order->orderServices->sum('price');
        $subtotal = round($totalWithVat / (1 + $vatPercent/100), 2);
        $tax = $totalWithVat - $subtotal;
        $sellerName   = 'شركة قمر سمايا للتزيين النسائي';
        $vatNumber    = '311127360700003';
        $timestampIso = $order->created_at->format('Y-m-d\TH:i:sP');
        $totalStr     = number_format($totalWithVat, 2, '.', '');
        $taxStr       = number_format($tax, 2, '.', '');
        $qrBase64 = \App\Helpers\ZatcaQr::base64($sellerName, $vatNumber, $timestampIso, $totalStr, $taxStr);
    @endphp

    <table>
        <thead>
        <tr>
            <th>الخدمة</th>
            <th>الكمية</th>
            <th>مقدم الخدمة</th>
            <th>السعر</th>
        </tr>
        </thead>
        <tbody>
        @foreach($order->orderServices as $orderService)
            @php
                $priceWithoutVat = round($orderService->price / (1 + $vatPercent/100), 2);
            @endphp
            <tr>
                <td>{{ $orderService->service?->name }}</td>
                <td>1</td>
                <td>
                    @if($orderService->employee && $orderService->employee->user)
                        {{ $orderService->employee->user->name }}<br>
                        <small class="muted">{{ $orderService->employee->user->phone }}</small>
                    @else
                        <span class="muted">غير معين</span>
                    @endif
                </td>
                <td>{{ number_format($priceWithoutVat, 2) }} ر.س</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr>
            <td>المجموع الفرعي (بدون الضريبة):</td>
            <td>{{ number_format($subtotal, 2) }} ر.س</td>
        </tr>
        <tr>
            <td>ضريبة القيمة المضافة ({{ $vatPercent }}%):</td>
            <td>{{ number_format($tax, 2) }} ر.س</td>
        </tr>
        <tr>
            <td colspan="2" style="text-align:center">
                <strong>المجموع الإجمالي (شامل الضريبة): {{ number_format($totalWithVat, 2) }} ر.س</strong>
            </td>
        </tr>
    </table>

    <div class="qr">
        <img src="https://api.qrserver.com/v1/create-qr-code/?data={{ urlencode($qrBase64) }}&size=200x200" alt="ZATCA QR">
        <div class="muted">رمز الاستجابة السريعة (ZATCA)</div>
    </div>

    <div class="footer">
        جميع الأسعار تشمل ضريبة القيمة المضافة 15% <br>
        شكراً لتعاملكم معنا
    </div>
</div>

{{-- ========================================== --}}
{{-- Employee Service Invoices (Professional Format per Service) --}}
{{-- ========================================== --}}
@if($order->orderServices && count($order->orderServices) > 0)
    @foreach($order->orderServices as $key => $orderService)
        @php
            $priceWithoutVat = round($orderService->price / (1 + 0.15), 2);
            $taxAmount = $orderService->price - $priceWithoutVat;
            $employeeName = $orderService->employee?->user?->name ?? 'غير محددة';
            $clientId = $order->id;
        @endphp

        <div class="employee-invoice"
             style="width:80mm;
                    margin:15px auto;
                    padding:10px;
                    font-size:11px;
                    font-family:'Tajawal', sans-serif;
                    text-align:center;
                    border-top:2px dashed #000;
                    page-break-inside:avoid;
                    page-break-after:always;">

            <div style="color:#777; font-size:10px; margin-bottom:4px;">نسخة الموظفة</div>
            <h6 style="margin:2px 0;">رقم العميلة: {{ $clientId }}</h6>
            <h6 style="margin:2px 0;">{{ $orderService->service?->name ?? '-' }}</h6>

            <table style="width:100%; border-collapse:collapse; font-size:11px; margin-top:5px;">
                <thead>
                    <tr style="background:#f2f2f2;">
                        <th style="border:1px solid #000; padding:4px;">الموظفة</th>
                        <th style="border:1px solid #000; padding:4px;">السعر قبل الضريبة</th>
                        <th style="border:1px solid #000; padding:4px;">الضريبة</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="border:1px solid #000; padding:4px;">{{ $employeeName }}</td>
                        <td style="border:1px solid #000; padding:4px;">{{ number_format($priceWithoutVat, 2) }}</td>
                        <td style="border:1px solid #000; padding:4px;">{{ number_format($taxAmount, 2) }}</td>
                    </tr>
                </tbody>
            </table>

            <div style="margin-top:10px;">
               {!! DNS1D::getBarcodeHTML($order->id . '-' . ($key + 1), 'C128', 2, 60) !!}

                <p style="font-size:11px; margin-top:3px;">{{ $order->id }}-{{ $key + 1 }}</p>
            </div>

            <p style="margin-top:5px; font-size:14px; font-weight:bold;">{{ $employeeName }}</p>
        </div>
    @endforeach
@endif

</body>
</html>
