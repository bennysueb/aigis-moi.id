<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use Barryvdh\DomPDF\Facade\Pdf; // Impor fasad PDF

class CertificateController extends Controller
{
    public function download(Registration $registration)
    {
        $data = [
            'registrantName' => $registration->name,
            'eventName' => $registration->event->name,
        ];

        $pdf = Pdf::loadView('certificate.template', $data);

        // Atur orientasi kertas menjadi landscape
        $pdf->setPaper('a4', 'landscape');

        return $pdf->stream('certificate-' . $registration->name . '.pdf');
    }
}
