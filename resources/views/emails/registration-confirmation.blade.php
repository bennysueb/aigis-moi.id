<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Confirmation for {{ $registration->event->name }}</title>
</head>

<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">

    @php
    // Menentukan tipe kehadiran final untuk digunakan di dalam view
    $attendanceType = $registration->event->type === 'hybrid'
    ? $registration->attendance_type
    : $registration->event->type;
    @endphp

    <table align="center" border="0" cellpadding="0" cellspacing="0" width="600" style="border-collapse: collapse; margin: 20px auto; border: 1px solid #cccccc; background-color: #ffffff;">
        {{-- Header --}}
        <tr>
            <td align="center" style="padding: 40px 0 30px 0; background-color: #00554E; color: #ffffff;">
                <h1 style="margin: 0; font-size: 24px;">Registration Successful!</h1>
            </td>
        </tr>
        {{-- Body Content --}}
        <tr>
            <td style="padding: 40px 30px 40px 30px;">
                <table border="0" cellpadding="0" cellspacing="0" width="100%">
                    <tr>
                        <td style="color: #333333; font-size: 16px;">
                            <h2 style="margin: 0 0 20px 0; font-size: 20px;">Halo, {{ $registration->name }}!</h2>
                            <p style="margin: 0 0 10px 0;">Thank you for registering. We have successfully received your confirmation for the event below.</p>
                        </td>
                    </tr>
                    {{-- Event Details --}}
                    <tr>
                        <td style="padding: 20px 0;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-top: 1px solid #eeeeee; border-bottom: 1px solid #eeeeee;">
                                <tr>
                                    <td style="padding: 15px 0;">
                                        <b style="color: #00554E;">Event Name:</b><br>
                                        {{ $registration->event->name }}
                                    </td>
                                </tr>
                                <tr>
                                    <<td style="padding: 15px 0;">
                                        <b style="color: #00554E;">Date & Time:</b><br>

                                        {{-- Cek apakah tanggal mulai dan selesai ada di hari yang sama --}}
                                        @if ($registration->event->start_date->isSameDay($registration->event->end_date))

                                        {{-- Jika di hari yang sama, tampilkan tanggal sekali saja --}}
                                        {{ $registration->event->start_date->format('l, d F Y') }}
                                        <br>
                                        {{ $registration->event->start_date->format('H:i') }} - {{ $registration->event->end_date->format('H:i T') }}

                                        @else

                                        {{-- Jika di hari yang berbeda, tampilkan keduanya secara lengkap --}}
                                        {{ $registration->event->start_date->format('l, d F Y, H:i T') }}
                                        <br>
                                        to {{ $registration->event->end_date->format('l, d F Y, H:i T') }}

                                        @endif
                        </td>
                    </tr>

                    {{-- Menampilkan detail berdasarkan tipe kehadiran --}}
                    @if ($attendanceType === 'offline')
                    <tr>
                        <td style="padding: 15px 0;">
                            <b style="color: #00554E;">Lokasi:</b><br>
                            {{-- Mengambil data terjemahan venue --}}
                            {{ $registration->event->getTranslation('venue', 'id') ?: $registration->event->getTranslation('venue', 'en') }}
                        </td>
                    </tr>
                    @else
                    <tr>
                        <td style="padding: 15px 0; font-size: 14px; line-height: 1.5;">
                            <b style="color: #00554E;">Informasi Acara Online:</b><br>
                            <b>Platform:</b> {{ $registration->event->platform === 'Lainnya...' ? ($registration->event->meeting_info['platform_name'] ?? 'N/A') : $registration->event->platform }}<br>
                            @if($registration->event->meeting_link)
                            <b>Link:</b> <a href="{{ $registration->event->meeting_link }}" target="_blank" style="color: #007BFF;">Click to join</a><br>
                            @endif
                            @if ($registration->event->platform === 'Zoom Meeting')
                            <b>Meeting ID:</b> {{ $registration->event->meeting_info['meeting_id'] ?? '-' }}<br>
                            <b>Passcode:</b> {{ $registration->event->meeting_info['passcode'] ?? '-' }}<br>
                            @elseif ($registration->event->platform === 'Lainnya...')
                            <b>Instructions:</b> {{ $registration->event->meeting_info['instructions'] ?? '-' }}
                            @endif
                        </td>
                    </tr>
                    @endif
                </table>
            </td>
        </tr>

        {{-- Menampilkan QR Code hanya jika kehadiran offline --}}
        @if ($attendanceType === 'offline' && $qrCodeImage)
        {{-- QR Code Section --}}
        <tr>
            <td align="center" style="padding: 20px 0 30px 0;">
                <p style="margin: 0 0 15px 0; color: #333333;">Show this QR code at the entrance to check-in.</p>
                <img src="{{ $message->embedData($qrCodeImage, 'ticket-qrcode.png', 'image/png') }}" alt="QR Code Ticket" style="display: block;">
            </td>
        </tr>
        {{-- Button Section --}}
        <tr>
            <td align="center">
                <table border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td align="center" bgcolor="#00554E" style="border-radius: 5px;">
                            <a href="{{ route('tickets.qrcode', $registration->uuid) }}" target="_blank" style="font-size: 16px; color: #ffffff; text-decoration: none; padding: 15px 25px; border-radius: 5px; display: inline-block;">Lihat Tiket Online</a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        @endif
    </table>
    </td>
    </tr>
    {{-- Footer --}}
    <tr>
        <td align="center" style="padding: 20px 30px; background-color: #eeeeee; color: #555555; font-size: 12px;">
            <p style="margin: 0;">This email was automatically generated. Please do not reply to this email.</p>
            <p style="margin: 5px 0 0 0;">&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </td>
    </tr>
    </table>
</body>

</html>