<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject }}</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }

        a {
            color: #007BFF;
        }
    </style>
</head>

<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
    <div style="display: none; max-height: 0; overflow: hidden;">
        {{ strip_tags(Str::limit($content, 150)) }}
    </div>

    <table align="center" border="0" cellpadding="0" cellspacing="0" width="600" style="border-collapse: collapse; margin: 20px auto; border: 1px solid #cccccc; background-color: #ffffff;">
        <tr>
            <td align="center" style="padding: 20px 0; background-color: #eeeeeeff; color: #ffffff;">
                {{-- DIUBAH: Menggunakan $message->embed() untuk logo --}}
                @if($logoPath)
                <img src="{{ $message->embed($logoPath) }}" alt="{{ config('app.name') }} Logo" width="150" style="display: block;">
                @else
                <h1 style="margin: 0; font-size: 24px;">{{ config('app.name') }}</h1>
                @endif
            </td>
        </tr>

        {{-- DIUBAH: Menggunakan $message->embed() untuk banner --}}
        @if($bannerPath)
        <tr>
            <td>
                <img src="{{ $message->embed($bannerPath) }}" alt="Event Banner" width="600" style="display: block; width: 100%; height: auto;">
            </td>
        </tr>
        @endif

        <tr>
            <td style="padding: 40px 30px; color: #333333; font-size: 16px; line-height: 1.6;">
                {!! $content !!}
            </td>
        </tr>

        <tr>
            <td align="center" style="padding: 20px 30px; background-color: #eeeeee; color: #555555; font-size: 12px;">
                <p style="margin: 0;">This email is sent automatically. Please do not reply to this email.</p>
                <p style="margin: 5px 0 0 0;">&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            </td>
        </tr>
    </table>
</body>

</html>