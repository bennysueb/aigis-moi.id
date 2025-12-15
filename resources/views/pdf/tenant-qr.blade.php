<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>QR Code Toko - {{ $tenant->name }}</title>
    <style>
        @page {
            margin: 0px;
        }

        body {
            font-family: 'Helvetica', sans-serif;
            margin: 0px;
            background-color: #fff;
            color: #333;
            text-align: center;
        }

        .header {
            background-color: #2563eb;
            /* Warna biru branding */
            color: white;
            padding: 40px 20px;
            margin-bottom: 50px;
        }

        .event-name {
            font-size: 18px;
            text-transform: uppercase;
            letter-spacing: 2px;
            opacity: 0.9;
        }

        .shop-name {
            font-size: 42px;
            font-weight: bold;
            margin-top: 10px;
            margin-bottom: 0;
        }

        .content {
            padding: 0 50px;
        }

        .qr-box {
            border: 4px solid #333;
            display: inline-block;
            padding: 20px;
            border-radius: 20px;
            margin-bottom: 30px;
        }

        .qr-image {
            width: 400px;
            height: 400px;
        }

        .scan-me {
            font-size: 36px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        .instruction {
            font-size: 20px;
            color: #666;
            margin-bottom: 50px;
            line-height: 1.5;
        }

        .footer {
            position: absolute;
            bottom: 0;
            width: 100%;
            background-color: #f3f4f6;
            padding: 20px 0;
            font-size: 14px;
            color: #888;
            border-top: 1px solid #e5e7eb;
        }

        .booth-number {
            font-size: 24px;
            font-weight: bold;
            margin-top: 20px;
            padding: 10px 30px;
            background: #333;
            color: #fff;
            display: inline-block;
            border-radius: 50px;
        }
    </style>
</head>

<body>

    <div class="header">
        <div class="event-name">{{ $eventName }}</div>
        <h1 class="shop-name">{{ $tenant->name }}</h1>
    </div>

    <div class="content">
        <div class="scan-me">Scan to Shop</div>

        <div class="qr-box">
            <img src="data:image/svg+xml;base64,{{ $qrCode }}" class="qr-image" />
        </div>

        <div class="instruction">
            Arahkan kamera HP Anda ke kode QR di atas<br>
            untuk melihat <strong>Katalog Produk</strong> dan<br>
            melakukan <strong>Pemesanan Online</strong>.
        </div>

        {{-- Jika ada nomor booth di table user/tenant, tampilkan disini --}}
        {{-- <div class="booth-number">Booth A-23</div> --}}

        <p style="margin-top: 30px; color: #999; font-size: 14px;">
            {{ $url }}
        </p>
    </div>

    <div class="footer">
        Powered by {{ config('app.name') }} Digital Platform
    </div>

</body>

</html>