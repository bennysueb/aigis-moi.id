<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f3f4f6;
            color: #333;
            padding: 20px;
            line-height: 1.6;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .header {
            border-bottom: 2px solid #eee;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }

        .header h2 {
            margin: 0;
            color: #1f2937;
        }

        .details {
            background-color: #f9fafb;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
            font-size: 14px;
        }

        .details ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .details li {
            margin-bottom: 8px;
        }

        .btn-container {
            text-align: center;
            margin-top: 30px;
            margin-bottom: 30px;
        }

        .button {
            background-color: #2563eb;
            color: #ffffff !important;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            display: inline-block;
        }

        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 12px;
            color: #6b7280;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h2>Invoice Pendaftaran</h2>
        </div>

        <p>Halo, <strong>{{ $registration->name }}</strong></p>

        <p>Terima kasih telah mendaftar di acara <strong>{{ $registration->event->name }}</strong>. Pendaftaran Anda telah kami terima.</p>

        <p>Status pendaftaran Anda saat ini adalah: <strong style="color: #d97706;">MENUNGGU PEMBAYARAN (UNPAID)</strong>.</p>

        <div class="details">
            <h3>Rincian Tagihan:</h3>
            <ul>
                <li><strong>Event:</strong> {{ $registration->event->name }}</li>
                <li><strong>Jenis Tiket:</strong> {{ $registration->ticketTier->name ?? 'Standard' }}</li>
                <li><strong>Total Tagihan:</strong> Rp {{ number_format($registration->total_price, 0, ',', '.') }}</li>
            </ul>
        </div>

        <p>Mohon segera selesaikan pembayaran Anda untuk mendapatkan tiket masuk. Klik tombol di bawah ini untuk melihat invoice dan melakukan pembayaran:</p>

        <div class="btn-container">
            <a href="{{ route('invoice.show', $registration->uuid) }}" class="button">Bayar Sekarang / Lihat Invoice</a>
        </div>

        <p style="font-size: 13px; color: #666;">Jika tombol di atas tidak berfungsi, salin dan tempel link berikut ke browser Anda:<br>
            <a href="{{ route('invoice.show', $registration->uuid) }}">{{ route('invoice.show', $registration->uuid) }}</a>
        </p>

        <div class="footer">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.<br>
            Email ini dikirim secara otomatis, mohon tidak membalas email ini.
        </div>
    </div>
</body>

</html>