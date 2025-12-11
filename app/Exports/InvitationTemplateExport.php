<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Collection;

class InvitationTemplateExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    public function headings(): array
    {
        return [
            'name',
            'email',
            'phone',
            'company',
            'category',
        ];
    }

    public function collection()
    {
        // Data Dummy/Contoh agar user paham formatnya
        return new Collection([
            [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'phone' => '081234567890',
                'company' => 'PT. Contoh Sukses',
                'category' => 'VIP',
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'phone' => '628123456789',
                'company' => 'Universitas Indonesia',
                'category' => 'Speaker',
            ]
        ]);
    }
}
