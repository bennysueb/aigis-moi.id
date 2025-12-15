<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome, Exhibitor!</title>
</head>

<body style="font-family: Arial, sans-serif; color: #333; line-height: 1.6; text-align: center; background-color: #f4f4f4; margin: 0; padding: 20px;">

    <div style="max-width: 600px; margin: auto; background-color: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
        <h1 style="color: #0d6efd;">Welcome, {{ $user->name }} from {{ $user->nama_instansi }}!</h1>
        <p style="font-size: 16px;">
            Thank you for registering as an exhibitor. Your registration is complete.
        </p>
        <p style="font-size: 16px;">
            This is your unique **Booth QR Code**. Attendees can scan this code to connect with you.
        </p>

        <div style="padding: 20px; margin: 30px 0;">
            <img src="{{ $message->embedData($qrCodeData, 'qrcode.png', 'image/png') }}" alt="Your Personal QR Code">
        </div>

        <p style="font-size: 14px; color: #666;">
            You can manage your exhibitor profile and see attendee data from your dashboard.
        </p>
        <p style="margin-top: 30px;">
            We look forward to a successful event with you!
        </p>
    </div>

</body>

</html>