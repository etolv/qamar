<html lang="ar" dir="rtl">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barcode Generate</title>
    <!-- Load Noto Naskh Arabic Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Naskh+Arabic:wght@400..700&family=Tajawal:wght@200;300;400;500;700;800;900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Tajawal', sans-serif;
            direction: rtl;
            font-weight: 400;
            font-style: normal;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            text-align: center;
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
        }

        h3 {
            margin-bottom: 20px;
        }

        .barcode {
            margin: 20px 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .barcode>div {
            display: inline-block;
            overflow: visible;
            /* Ensure the entire barcode is visible */
        }

        .barcode-text {
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h3>{{ $product->name }}</h3>
        <div class="barcode">
            <div style="width: auto; height: auto;">
                <barcode code="{{$product->sku}}" type="C39" size="0.5" height="2.0" />
            </div>
        </div>
        <div class="barcode-text">
            <small>{{ $product->sku }}</small>
        </div>
    </div>
</body>

</html>