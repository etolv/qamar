<html>

<head>
    <title>{{ _t('Payment Required') }}</title>
    {{-- bootstrap 5 cdn --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>

<body>
    <!-- Error -->
    <div class="container-xxl container-p-y">
        <div class="misc-wrapper">
            <img src="{{ asset('assets/img/qamar_samaya_logo-light.svg') }}" width="100" alt="page-forbidden">
            <h2 class="mb-1 mt-2">{{ _t('Payment Required') }}</h2>
            <p class="mb-2 mx-2">
                {{ _t('You have new payment required for order') }} #{{ $payment->model_id }}
            </p>
            <p class="mb-2 mx-2">
                {{ _t('Amount') }}: {{ $payment->amount }} {{ _t('SAR') }}
            </p>
            @php
                $data = json_decode($payment->data);
                $payment_url = $data ? $data->payment_url : url('/');
            @endphp
            <a href="{{ $payment_url }}" class="btn btn-primary mb-4">{{ _t('Pay now') }}</a>
            <div class="mt-4">
                <span class="app-brand-logo demo">
                </span>
            </div>
        </div>
    </div>
</body>

</html>
