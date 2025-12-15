<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome!</title>
</head>

<body style="font-family: Arial, sans-serif; color: #333; line-height: 1.6; text-align: center; background-color: #f4f4f4; margin: 0; padding: 20px;">

    <div style="max-width: 600px; margin: auto; background-color: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
        <h1 style="color: #0d6efd;">Welcome, {{ $user->name }}!</h1>
        <p style="font-size: 16px;">
            Thank you for registering. Your registration is complete and your personal QR code has been generated.
        </p>
        <p style="font-size: 16px;">
            Please present this QR code at the event for check-in and interactions with exhibitors.
        </p>

        {{-- Menggunakan $message->embedData dengan data yang sudah disiapkan dari Mailable --}}
        <div style="padding: 20px; margin: 30px 0;">
            <img src="{{ $message->embedData($qrCodeData, 'qrcode.png', 'image/png') }}" alt="Your Personal QR Code">
        </div>

        <p style="font-size: 14px; color: #666;">
            You can also access this QR code from your dashboard at any time.
        </p>
        <p style="margin-top: 30px;">
            Thank you!
        </p>
    </div>

</body>

</html>