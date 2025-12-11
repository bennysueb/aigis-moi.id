<!DOCTYPE html>
<html>

<head>
    <title>Certificate</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            text-align: center;
        }

        .container {
            border: 20px solid #4A7C59;
            width: 750px;
            height: 550px;
            margin: auto;
            padding: 30px;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }

        .marquee {
            color: #4A7C59;
            font-size: 48px;
            margin: 20px 0;
        }

        .person {
            font-size: 42px;
            font-weight: bold;
            color: #333;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }

        .reason {
            margin-top: 20px;
            font-size: 20px;
            color: #555;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="logo">AIGIS 2026 SUMMIT</div>
        <div class="marquee">Certificate of Participation</div>
        <div class="person">{{ $registrantName }}</div>
        <div class="reason">
            is hereby granted this certificate for successfully participating in the<br />
            <strong>{{ $eventName }}</strong>
        </div>
    </div>
</body>

</html>