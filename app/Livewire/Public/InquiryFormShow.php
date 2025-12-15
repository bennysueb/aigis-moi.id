<?php

namespace App\Livewire\Public;

use App\Models\InquiryForm;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use App\Rules\SlotAvailable;
use App\Models\InquirySubmission;

class InquiryFormShow extends Component
{
    use WithFileUploads;


    public InquiryForm $form;
    public array $formData = [];
    public bool $success = false;

    public array $bookedSlots = [];

    public function mount(InquiryForm $form)
    {
        $this->form = $form;
        
        foreach ($form->fields as $field) {
            
            if ($field['type'] === 'checkbox-multiple') {
                $this->formData[$field['name']] = [];
            } else {
                $this->formData[$field['name']] = '';
            }

            if (!empty($field['enable_slot_validation'])) {
                $this->loadBookedSlots($field['name']);
            }
        }
    }

    public function loadBookedSlots($fieldName)
    {
        // Ambil semua submission yang relevan
        $submissions = \App\Models\InquirySubmission::where('inquiry_form_id', $this->form->id)->get();

        // Proses data dengan aman menggunakan collection method
        $this->bookedSlots = $submissions
            ->map(function ($submission) use ($fieldName) {
                // Gunakan null coalescing operator (??) untuk mencegah error jika key tidak ada.
                // Jika $submission->data[$fieldName] ada, ambil nilainya. Jika tidak, kembalikan null.
                return $submission->data[$fieldName] ?? null;
            })
            ->filter() // Hapus semua nilai null atau kosong dari hasil.
            ->flatten()
            ->toArray();
    }

    public function submit()
    {
        // 1. Inisialisasi variabel yang dibutuhkan
        $rules = [];
        $textData = [];
        $fileData = [];
        $signatureData = [];

        // 2. Loop Tunggal untuk Validasi dan Pemrosesan Data
        foreach ($this->form->fields as $field) {
            $fieldName = $field['name'];
            $fieldType = $field['type'];

            // Lewati field statis yang tidak butuh input atau validasi
            if (in_array($fieldType, ['heading', 'paragraph'])) {
                continue;
            }

            // Bangun aturan validasi untuk field yang memiliki input
            $rule = $field['required'] ? ['required'] : ['nullable'];
            if ($field['required']) {
                $rule[] = 'required';
            } else {
                $rule[] = 'nullable';
            }

            if ($fieldType === 'checkbox-multiple' && $field['required']) {
                // Pastikan minimal satu opsi dipilih jika required
                $rule[] = 'array';
                $rule[] = 'min:1'; 
            }
            
            switch ($fieldType) {
                case 'email':
                    $rule[] = 'email';
                    break;
                case 'image':
                    $rule[] = 'image';
                    $rule[] = 'max:2048'; // 2MB Max
                    break;
                case 'file':
                    $rule[] = 'file';
                    $rule[] = 'max:5120'; // 5MB Max
                    break;
            }
            if (!empty($field['enable_slot_validation'])) {
                $rule[] = new SlotAvailable($this->form->id, $fieldName);
            }
            $rules['formData.' . $fieldName] = $rule;

            // Proses dan pisahkan data input jika ada
            if (isset($this->formData[$fieldName]) && $this->formData[$fieldName] !== null) {
                $value = $this->formData[$fieldName];

                if (in_array($fieldType, ['file', 'image'])) {
                    $fileData[$fieldName] = $value;
                } elseif ($fieldType === 'signature') {
                    $signatureData[$fieldName] = $value;
                } else {
                    $textData[$fieldName] = $value;
                }
            }
        }

        // 3. Lakukan Validasi
        $this->validate($rules);

        // 4. Buat record submission dengan data teks
        $submission = $this->form->submissions()->create(['data' => $textData]);

        // 5. Lampirkan File dan Gambar
        // Spatie Media Library cukup pintar untuk menangani objek TemporaryUploadedFile dari Livewire
        foreach ($fileData as $key => $file) {
            $submission->addMedia($file)
                ->usingName($file->getClientOriginalName())
                ->toMediaCollection($key);
        }

        // 6. Lampirkan Tanda Tangan
        foreach ($signatureData as $key => $dataUrl) {
            $submission->addMediaFromBase64($dataUrl)
                ->usingFileName($key . '.png')
                ->toMediaCollection($key);
        }

        // 7. Set status sukses untuk menampilkan pesan ke pengguna
        $this->success = true;
    }


    public function render()
    {
        return view('livewire.public.inquiry-form-show')->layout('layouts.app');
    }
}
