<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class FeedbackResultController extends Controller
{
    public function show(Event $event)
    {
        // 1. Validasi: Pastikan event ini memang punya form feedback
        if (!$event->feedbackForm) {
            abort(404, 'Feedback form for this event is not available.');
        }

        // 2. Ambil semua data yang relevan
        $submissions = $event->feedbackSubmissions()->with('registration')->get();
        $formFields = $event->feedbackForm->fields; // Struktur pertanyaan
        $totalSubmissions = $submissions->count();

        // 3. Proses dan Agregasi Data
        $aggregatedData = [];
        if ($totalSubmissions > 0) {
            foreach ($formFields as $field) {
                $fieldName = $field['name'];
                $fieldLabel = $field['label'];
                $fieldType = $field['type'];

                $responses = $submissions->pluck('data.' . $fieldName)->filter();

                // Inisialisasi data untuk field ini
                $aggregatedData[$fieldName] = [
                    'label' => $fieldLabel,
                    'type' => $fieldType,
                    'total_responses' => $responses->count(),
                    'data' => [],
                ];

                // Agregasi berdasarkan tipe pertanyaan
                if (in_array($fieldType, ['radio', 'select', 'checkbox'])) {
                    // Hitung jumlah untuk setiap pilihan jawaban
                    $chartData = $responses->flatten()->countBy();

                    // Siapkan data dalam format yang siap pakai untuk view
                    $aggregatedData[$fieldName]['data'] = [
                        'labels' => $chartData->keys(),
                        'values' => $chartData->values()
                    ];
                } elseif ($fieldType === 'rating') {
                    // Hitung rata-rata rating
                    $numericResponses = $responses->map(fn($val) => (int)$val)->filter(fn($val) => $val > 0);
                    $aggregatedData[$fieldName]['data'] = [
                        'average' => $numericResponses->avg() ?: 0,
                        'distribution' => $numericResponses->countBy(),
                    ];
                } else {
                    // Untuk tipe text/textarea, kita hanya kumpulkan semua jawaban (opsional)
                    $aggregatedData[$fieldName]['data'] = $responses->all();
                }
            }
        }

        // 4. Kirim data ke view
        return view('feedback.results-public', [
            'event' => $event,
            'totalSubmissions' => $totalSubmissions,
            'aggregatedData' => $aggregatedData,
        ]);
    }
}
