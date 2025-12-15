<?php

namespace App\Http\Controllers;

use App\Exports\SubmissionsExport;
use App\Models\InquiryForm;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class InquiryFormController extends Controller
{
    // ... (metode lain jika ada)

    public function exportSubmissions(InquiryForm $form)
    {
        $fileName = $form->slug . '-submissions.xlsx';
        return Excel::download(new SubmissionsExport($form), $fileName);
    }
}
