<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\InquirySubmission;

class SlotAvailable implements Rule
{
    protected $formId;
    protected $fieldName;

    /**
     * Buat instance rule baru.
     *
     * @param int $formId ID dari formulir yang sedang divalidasi.
     * @param string $fieldName Nama dari field yang nilainya harus unik (e.g., 'jadwal_appointment').
     */
    public function __construct($formId, $fieldName)
    {
        $this->formId = $formId;
        $this->fieldName = $fieldName;
    }

    /**
     * Menentukan apakah aturan validasi lolos.
     *
     * @param  string  $attribute Nama atribut (otomatis dari Laravel).
     * @param  mixed  $value Nilai yang dipilih oleh pengguna.
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // Query untuk mengecek apakah slot dengan nilai ($value) sudah ada.
        $isTaken = InquirySubmission::where('inquiry_form_id', $this->formId)
            ->whereJsonContains('data->' . $this->fieldName, $value)
            ->exists();

        // Aturan ini "lolos" (passes) jika slotnya BELUM diambil (!isTaken).
        return !$isTaken;
    }

    /**
     * Mendapatkan pesan error validasi.
     *
     * @return string
     */
    public function message()
    {
        return 'Pilihan ini sudah tidak tersedia. Silakan pilih yang lain.';
    }
}
