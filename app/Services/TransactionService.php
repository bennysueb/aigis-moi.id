<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\User; // <-- Tambahkan import ini
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TransactionService
{
    protected $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }

    /**
     * Proses Checkout Utama (Polimorfik)
     * $payable: Bisa berupa object Registration atau ProductOrder
     * $payer: Bisa berupa User (member) atau Registration/Order (guest)
     */
    public function createTransaction($payer, $payable, $amount)
    {
        return DB::transaction(function () use ($payer, $payable, $amount) {

            // 1. Buat Order ID Unik (Format: TRX-TIMESTAMP-RANDOM)
            $orderId = 'TRX-' . time() . '-' . Str::upper(Str::random(5));

            // 2. Siapkan Parameter Payload untuk Midtrans
            $params = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => (int) $amount, // Midtrans butuh integer
                ],
                'customer_details' => [
                    'first_name' => $payer->name,
                    'email' => $payer->email,
                    'phone' => $payer->phone_number ?? '',
                ],
            ];

            // 3. Minta Snap Token ke Midtrans
            $snapToken = $this->midtransService->getSnapToken($params);

            // 4. Tentukan User ID (NULL jika Guest)
            // --- PERBAIKAN DI SINI ---
            $userId = ($payer instanceof User) ? $payer->id : null;

            // 5. Simpan ke Database Transaksi Pusat
            $transaction = Transaction::create([
                'id' => $orderId,
                'user_id' => $userId, // Gunakan variabel yang sudah divalidasi
                'payable_type' => get_class($payable),
                'payable_id' => $payable->id,
                'amount' => $amount,
                'midtrans_transaction_id' => null,
                'snap_token' => $snapToken,
                'status' => 'pending',
                'payload' => json_encode($params),
            ]);

            return $transaction;
        });
    }
}
