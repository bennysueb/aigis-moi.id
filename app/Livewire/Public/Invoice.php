<?php

namespace App\Livewire\Public;

use App\Models\Registration;
use App\Models\Transaction;
use App\Models\EventEmailTemplate; // <--- PENTING: Untuk ambil template email
use App\Mail\DynamicBroadcastMail; // <--- PENTING: Class untuk kirim email tiket
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail; // <--- PENTING: Facade untuk kirim email
use Livewire\Component;
use Livewire\Attributes\Layout;

class Invoice extends Component
{
    public Registration $registration;

    public function mount(Registration $registration, Request $request, MidtransService $midtransService)
    {
        $this->registration = $registration->load(['event', 'transaction', 'ticketTier']);

        // --- LOGIKA UTAMA: Cek Status & Kirim Email ---
        
        // Cek 1: Apakah status di DB masih 'unpaid'?
        // Cek 2: Apakah URL dari Midtrans bilang 'settlement' atau 'capture'?
        if ($this->registration->payment_status == 'unpaid' && 
            ($request->query('transaction_status') == 'settlement' || $request->query('transaction_status') == 'capture')) {
            
            $orderId = $request->query('order_id') ?? optional($this->registration->transaction)->id;

            if ($orderId) {
                // Validasi ke Server Midtrans (Biar aman, gak cuma percaya URL)
                $midtransStatus = $midtransService->getStatus($orderId);

                if ($midtransStatus && ($midtransStatus->transaction_status == 'settlement' || $midtransStatus->transaction_status == 'capture')) {
                    
                    // 1. Update Status Pendaftaran jadi PAID
                    $this->registration->update([
                        'payment_status' => 'paid',
                        'status' => 'confirmed'
                    ]);
                    
                    // 2. Update Status Transaksi jadi PAID
                    if($this->registration->transaction) {
                        $this->registration->transaction->update(['status' => 'paid']);
                    }

                    // 3. --- FITUR BARU: KIRIM EMAIL TIKET DI SINI ---
                    try {
                        $event = $this->registration->event;
                        
                        // Cek apakah Admin sudah mengatur Template Email Konfirmasi di Event ini
                        if ($event && $event->confirmation_template_id) {
                            $template = EventEmailTemplate::find($event->confirmation_template_id);
                            
                            if ($template) {
                                Mail::to($this->registration->email)
                                    ->send(new DynamicBroadcastMail($template, $this->registration));
                                
                                Log::info('Ticket email sent via Invoice Page to ' . $this->registration->email);
                            }
                        } else {
                            Log::warning('Ticket email NOT sent: No Confirmation Template ID set for Event ID ' . $event->id);
                        }
                    } catch (\Exception $e) {
                        Log::error('Failed sending ticket email from Invoice Page: ' . $e->getMessage());
                    }
                    // ------------------------------------------------

                    // 4. Refresh data agar tampilan berubah jadi LUNAS
                    $this->registration->refresh();
                    
                    // 5. Beri notifikasi ke user
                    session()->flash('message', 'Pembayaran berhasil! Tiket telah dikirim ke email Anda.');
                }
            }
        }

        // Validasi User (Opsional: Keamanan agar orang lain tidak lihat invoice ini)
        if (auth()->check() && auth()->id() !== $this->registration->user_id) {
             // abort(403); 
        }
    }

    public function cancel()
    {
        if ($this->registration->payment_status === 'paid') {
            $this->dispatch('swal:error', message: 'Tidak dapat membatalkan pesanan yang sudah dibayar.');
            return;
        }

        $this->registration->update(['status' => 'canceled']);

        return redirect()->route('order.cancelled', $this->registration->uuid);
    }
    
    public function payNow()
    {
        $snapToken = $this->registration->transaction->snap_token;
        $this->dispatch('trigger-payment', snap_token: $snapToken);
    }

    public function render()
    {
        return view('livewire.public.invoice')
            ->layout('layouts.blank', [
                'title' => 'Invoice - ' . $this->registration->event->name
            ]);
    }
}