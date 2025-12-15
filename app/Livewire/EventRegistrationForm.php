<?php

namespace App\Livewire;

use App\Models\Event;
use App\Models\User;
use App\Models\Registration;
use App\Notifications\NewRegistrationNotification;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Mail;
use App\Mail\RegistrationConfirmationMail;
use App\Mail\InvoiceMail; // <-- TAMBAHAN: Import InvoiceMail
use Illuminate\Support\Facades\Log;


use App\Models\TicketTier;
use App\Models\Voucher;
use App\Services\TransactionService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;



class EventRegistrationForm extends Component
{
    use WithFileUploads;


    public Event $event;
    // Properti untuk field standar
    public $name = '';
    public $email = '';
    public $phone_number = '';
    // Payment Data
    public $selectedTierId;
    public $voucherCode;
    public $voucherApplied = null;
    public $summary = [
        'price' => 0,
        'discount' => 0,
        'total' => 0
    ];
    // Properti untuk field kustom
    public array $formData = [];
    public bool $success = false;
    public array $combinedFields = [];

    public $attendance_type = 'offline'; // Default 'offline', akan relevan jika event hybrid



    public function mount()
    {
        // Inisialisasi formData untuk field dari InquiryForm (jika ada)
        if ($this->event->inquiryForm) {
            foreach ($this->event->inquiryForm->fields as $field) {
                $this->formData[$field['name']] = '';
            }
        }

        // BARU: Inisialisasi formData untuk field tambahan dari field_config (jika ada)
        if (!empty($this->event->field_config)) {
            foreach ($this->event->field_config as $fieldName => $config) {
                if ($config['active']) {
                    $this->formData[$fieldName] = '';
                }
            }
        }

        // Isi otomatis jika user sudah login
        if (auth()->check()) {
            $user = auth()->user();
            $this->name = $user->name;
            $this->email = $user->email;
            $this->phone_number = $user->phone_number;
        }

        if ($this->event->is_paid_event && $this->event->ticketTiers->count() > 0) {
            $this->selectedTierId = $this->event->ticketTiers->first()->id;
            $this->calculateSummary();
        }

        $fieldConfig = $this->event->field_config ?? [];
        $this->combinedFields = [];
        $processedConfigFields = [];

        // Prioritas 1: Proses field tambahan dari field_config
        foreach ($fieldConfig as $name => $config) {
            if ($config['active']) {
                $this->formData[$name] = '';

                $type = match ($name) {
                    'tipe_instansi' => 'select',
                    'alamat'        => 'textarea',
                    'tanda_tangan'  => 'signature',
                    default         => 'text',
                };

                $options = [];
                if ($name === 'tipe_instansi' && !empty($config['options'])) {
                    $options = array_map('trim', explode(',', $config['options']));
                }

                // ==========================================================
                // --- INI ADALAH PERBAIKANNYA ---
                // Secara eksplisit tambahkan 'Others' ke dalam array options
                if ($name === 'tipe_instansi') {
                    $options[] = 'Others';
                }
                // ==========================================================

                $this->combinedFields[] = [
                    'name' => $name,
                    'label' => Str::title(str_replace('_', ' ', $name)),
                    'type' => $type,
                    'options' => $options,
                    'required' => $config['required'],
                ];
                $processedConfigFields[] = $name;
            }
        }

        // Prioritas 2: Proses field kustom dari InquiryForm (tidak berubah)
        if ($this->event->inquiryForm) {
            foreach ($this->event->inquiryForm->fields as $field) {
                if (!in_array($field['name'], $processedConfigFields)) {
                    $this->formData[$field['name']] = '';
                    $this->combinedFields[] = $field;
                }
            }
        }
    }

    // Hitung ulang saat tiket berubah
    public function updatedSelectedTierId()
    {
        $this->calculateSummary();
    }

    // Fitur Voucher
    public function applyVoucher()
    {
        $this->validate(['voucherCode' => 'required|string']);

        $voucher = Voucher::where('code', $this->voucherCode)
            ->where('is_active', true)
            ->first();

        // Validasi Sederhana (Bisa diperkompleks sesuai request sebelumnya)
        if (!$voucher) {
            $this->addError('voucherCode', 'Kode voucher tidak ditemukan.');
            return;
        }

        // Cek Expired & Limit (Panggil helper di Model Voucher Fase 2)
        if (!$voucher->isValidForUser(Auth::id())) {
            $this->addError('voucherCode', 'Voucher tidak valid atau sudah habis.');
            return;
        }

        $this->voucherApplied = $voucher;
        $this->calculateSummary();

        // SweetAlert (lewat browser event)
        $this->dispatch('swal:success', message: 'Voucher berhasil dipasang!');
    }

    public function removeVoucher()
    {
        $this->voucherApplied = null;
        $this->voucherCode = '';
        $this->calculateSummary();
    }

    // Hitung Total Bayar
    public function calculateSummary()
    {
        if (!$this->event->is_paid_event) {
            return;
        }

        $tier = TicketTier::find($this->selectedTierId);
        $price = $tier ? $tier->price : 0;
        $discount = 0;

        if ($this->voucherApplied) {
            if ($this->voucherApplied->type == 'percentage') {
                $discount = ($price * $this->voucherApplied->amount) / 100;
            } else {
                $discount = $this->voucherApplied->amount;
            }
        }

        // Pastikan tidak minus
        $total = max(0, $price - $discount);

        $this->summary = [
            'price' => $price,
            'discount' => $discount,
            'total' => $total
        ];
    }

    private function updateUserProfileFromRegistration(Registration $registration)
    {
        // Cek apakah pendaftaran ini terhubung ke seorang pengguna
        if ($user = $registration->user) {
            // Daftar field yang ingin kita sinkronkan
            $fieldsToSync = ['nama_instansi', 'tipe_instansi', 'jabatan', 'alamat', 'tanda_tangan', 'phone_number'];

            $profileNeedsUpdate = false;

            foreach ($fieldsToSync as $field) {
                // Cek jika field di profil pengguna masih kosong DAN ada data baru dari form
                if (empty($user->{$field}) && !empty($registration->{$field} ?? $registration->data[$field] ?? null)) {

                    // Ambil data baru
                    $newData = $registration->{$field} ?? $registration->data[$field];

                    // Update properti di model User
                    $user->{$field} = $newData;
                    $profileNeedsUpdate = true;
                }
            }

            // Simpan ke database hanya jika ada perubahan
            if ($profileNeedsUpdate) {
                $user->save();
            }
        }
    }

    public function register(TransactionService $transactionService)
    {
        // 1. --- VALIDASI ---
        if ($this->event->quota > 0 && $this->event->remaining_quota <= 0) {
            $this->addError('quota', 'Sorry, the registration quota for this event is full.');
            return;
        }

        $rules = [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('registrations')
                    ->where('event_id', $this->event->id)
                    ->where(function ($query) {
                        return $query->whereNotIn('status', ['canceled', 'expired', 'rejected']);
                    }),
            ],
            'phone_number' => 'nullable|string|max:20',
            'selectedTierId' => $this->event->is_paid_event ? 'required|exists:ticket_tiers,id' : 'nullable',
        ];

        if ($this->event->type === 'hybrid') {
            $rules['attendance_type'] = 'required|in:offline,online';
        }

        $this->validate($rules);

        // Validasi Custom Fields
        $customRules = [];
        // ... (Validasi Custom Fields TETAP SAMA) ...
        $fieldConfig = $this->event->field_config ?? [];
        foreach ($fieldConfig as $fieldName => $config) {
            if (isset($config['active']) && $config['active'] && isset($config['required']) && $config['required']) {
                $customRules['formData.' . $fieldName] = 'required';
                if ($fieldName === 'tipe_instansi' && ($this->formData['tipe_instansi'] ?? '') === 'others') {
                    $customRules['formData.tipe_instansi_other'] = 'required';
                }
            }
        }
        if ($this->event->inquiryForm) {
            foreach ($this->event->inquiryForm->fields as $field) {
                if (isset($field['required']) && $field['required']) {
                    $customRules['formData.' . $field['name']] = 'required';
                }
            }
        }
        if (!empty($customRules)) {
            $this->validate($customRules);
        }


        // 2. --- PROSES PENYIMPANAN & PEMBAYARAN ---

        try {
            $result = DB::transaction(function () use ($transactionService) {
                $existingUser = User::where('email', $this->email)->first();
                $userId = $existingUser ? $existingUser->id : (Auth::id() ?? null);

                $registrationData = [
                    'event_id' => $this->event->id,
                    'user_id' => $userId,
                    'name' => $this->name,
                    'email' => $this->email,
                    'phone' => $this->phone_number,
                    'data' => $this->formData,
                    'attendance_type' => $this->event->type === 'hybrid' ? $this->attendance_type : $this->event->type,
                    'ticket_tier_id' => $this->selectedTierId,
                    'total_price' => $this->summary['total'] ?? 0,
                    'status' => 'pending',
                    'payment_status' => 'unpaid',
                ];

                $newRegistration = Registration::create($registrationData);
                $this->updateUserProfileFromRegistration($newRegistration);

                $snapToken = null;

                if ($this->event->is_paid_event && $this->summary['total'] > 0) {
                    // --- BERBAYAR ---
                    $payer = $existingUser ?? $newRegistration;
                    $transaction = $transactionService->createTransaction(
                        $payer,
                        $newRegistration,
                        $this->summary['total']
                    );

                    // Kirim Email Invoice
                    try {
                        Mail::to($newRegistration->email)->queue(new InvoiceMail($newRegistration));
                    } catch (\Exception $e) {
                        Log::error('Failed to send invoice email: ' . $e->getMessage());
                    }

                    $snapToken = $transaction->snap_token;
                } else {
                    // --- GRATIS ---
                    $newRegistration->update([
                        'status' => 'confirmed',
                        'payment_status' => 'paid'
                    ]);

                    // Kirim Email Tiket
                    try {
                        if ($this->event->confirmation_template_id) {
                            $template = \App\Models\EventEmailTemplate::find($this->event->confirmation_template_id);
                            if ($template) {
                                Mail::to($newRegistration->email)->queue(new \App\Mail\DynamicBroadcastMail($template, $newRegistration));
                            }
                        }
                    } catch (\Exception $e) {
                        Log::error('Email Failed: ' . $e->getMessage());
                    }
                }

                return [
                    'token' => $snapToken,
                    'registration' => $newRegistration
                ];
            });

            $snapToken = $result['token'];
            $registration = $result['registration'];

            // 3. --- RESPONSE KE BROWSER ---

            if ($snapToken) {
                // KASUS BERBAYAR: Redirect ke Halaman Invoice (Bukan Popup)
                // Kita tidak pakai dispatch('start-payment') lagi di sini
                return redirect()->route('invoice.show', $registration->uuid);
            } else {
                // KASUS GRATIS: Redirect Sukses
                $this->dispatch('registration-success');
                return redirect()->route('events.register.success', [
                    'event' => $this->event->slug,
                    'registration' => $registration->uuid,
                ]);
            }
        } catch (\Exception $e) {
            $this->dispatch('swal:error', message: 'Terjadi kesalahan: ' . $e->getMessage());
            Log::error('Registration Error: ' . $e->getMessage());
        }
    }


    public function render()
    {
        return view('livewire.event-registration-form');
    }
}
