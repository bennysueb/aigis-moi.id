<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TenantQrController extends Controller
{
    public function downloadPdf()
    {
        $tenant = Auth::user()->tenant;

        if (!$tenant) {
            abort(404, 'Data toko tidak ditemukan.');
        }

        // 1. URL Toko Publik (yang akan dibuka saat QR discan)
        $shopUrl = route('tenant.shop', $tenant->slug);

        // 2. Generate QR Code dalam bentuk Base64 agar bisa masuk ke PDF
        // Format SVG biasanya lebih tajam, tapi PNG lebih kompatibel dengan DomPDF lama
        // Kita gunakan format SVG lalu encode ke base64
        $qrCode = base64_encode(QrCode::format('svg')->size(400)->errorCorrection('H')->generate($shopUrl));

        // 3. Siapkan Data untuk View
        $data = [
            'tenant' => $tenant,
            'qrCode' => $qrCode,
            'url' => $shopUrl,
            'eventName' => config('app.name') // Atau ambil dari Model Event jika tenant terikat event
        ];

        // 4. Generate PDF
        $pdf = Pdf::loadView('pdf.tenant-qr', $data);

        // Set ukuran A4 Portrait
        $pdf->setPaper('a4', 'portrait');

        // 5. Download / Stream
        return $pdf->stream('QR-Toko-' . $tenant->slug . '.pdf');
    }
}
