<!DOCTYPE html>
<html>

<head>
    <title>Undangan Event</title>
</head>

<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
        <h2 style="color: #2d3748;">{{ $event->name }}</h2>

        <p>Yth. {{ $invitation->name }},</p>

        @if($customContent)
        {{-- Tampilkan konten dinamis dari inputan admin, ganti {link} dengan tombol --}}
        <div style="margin: 20px 0;">
            {!! nl2br(e(str_replace('{link}', '', $customContent))) !!}
        </div>
        @else
        <p>Kami dengan hormat mengundang Bapak/Ibu untuk hadir dalam acara <strong>{{ $event->name }}</strong> yang akan diselenggarakan pada:</p>
        <ul>
            <li><strong>Tanggal:</strong> {{ $event->start_date->format('d F Y') }}</li>
            <li><strong>Waktu:</strong> {{ $event->start_date->format('H:i') }} WIB</li>
            <li><strong>Tempat:</strong> {{ $event->venue['name'] ?? 'TBA' }}</li>
        </ul>
        @endif

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $confirmationLink }}" style="background-color: #3182ce; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold;">
                Konfirmasi Kehadiran
            </a>
        </div>

        <p style="font-size: 12px; color: #718096; text-align: center;">
            Jika tombol di atas tidak berfungsi, salin tautan ini ke browser Anda:<br>
            {{ $confirmationLink }}
        </p>
    </div>
</body>

</html>