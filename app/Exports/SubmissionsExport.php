<?php

namespace App\Exports;

use App\Models\InquiryForm;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SubmissionsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $form;
    protected $headings;

    public function __construct(InquiryForm $form)
    {
        $this->form = $form;
        // Buat headings (judul kolom) secara dinamis dari definisi field formulir
        $this->headings = collect($this->form->fields)->pluck('label')->prepend('Submitted At')->toArray();
    }

    public function collection()
    {
        return $this->form->submissions()->latest()->get();
    }

    public function headings(): array
    {
        return $this->headings;
    }

    public function map($submission): array
    {
        $mappedData = [];
        // Tambahkan tanggal submit sebagai kolom pertama
        $mappedData[] = $submission->created_at->format('Y-m-d H:i:s');

        // Petakan setiap data submission sesuai urutan headings
        foreach ($this->form->fields as $field) {
            $mappedData[] = $submission->data[$field['name']] ?? '';
        }

        return $mappedData;
    }
}
