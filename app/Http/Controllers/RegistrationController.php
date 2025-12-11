<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class RegistrationController extends Controller
{
    public function showQrCode(Registration $registration)
    {
        // URL ini akan menjadi tujuan saat QR code di-scan.
        // Kita akan membuat halaman check-in di URL ini nanti.
        $checkinUrl = route('checkin.scan', $registration->uuid);

        // Membuat QR code dalam format SVG
        $qrCode = QrCode::size(300)->format('svg')->generate($checkinUrl);

        return view('registration.qrcode', [
            'qrCode' => $qrCode,
            'registration' => $registration,
        ]);
    }

    public function scanCheckIn(Registration $registration)
    {
        // This is a placeholder for the check-in logic.
        // For now, it just confirms the ticket is valid.
        return response('QR Code valid untuk ' . $registration->name);
    }
}
