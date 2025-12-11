<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Registration;
use App\Models\ProductOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Models\EventEmailTemplate;
use App\Mail\DynamicBroadcastMail;

class PaymentCallbackController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->all();
        
        // Log singkat untuk memantau aktivitas masuk
        Log::info('Midtrans Callback received', ['order_id' => $payload['order_id'] ?? 'unknown']);

        $orderId = $payload['order_id'] ?? null;
        $transactionStatus = $payload['transaction_status'] ?? null;
        $type = $payload['payment_type'] ?? null;

        if (!$orderId) {
            return response()->json(['message' => 'Order ID missing'], 400);
        }

        // Cari transaksi berdasarkan ID string (TRX-...)
        $transaction = Transaction::where('id', $orderId)->first();

        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        $transaction->midtrans_transaction_id = $payload['transaction_id'] ?? null;
        $transaction->payment_type = $type;
        $transaction->payload = json_encode($payload);

        // Tentukan status baru berdasarkan respons Midtrans
        $newStatus = $transaction->status;

        if ($transactionStatus == 'capture') {
            if ($type == 'credit_card') {
                $newStatus = ($payload['fraud_status'] == 'challenge') ? 'pending' : 'paid';
            }
        } else if ($transactionStatus == 'settlement') {
            $newStatus = 'paid';
        } else if ($transactionStatus == 'pending') {
            $newStatus = 'pending';
        } else if ($transactionStatus == 'deny' || $transactionStatus == 'expire' || $transactionStatus == 'cancel') {
            $newStatus = 'failed';
        }

        // Simpan status transaksi
        $transaction->status = $newStatus;
        $transaction->save();

        // *** LOGIKA UPDATE ENTITAS & KIRIM EMAIL ***

        if ($newStatus == 'paid') {
            $payable = $transaction->payable;

            if ($payable instanceof Registration) {
                // Hanya proses jika status pendaftaran belum 'confirmed' (mencegah double email)
                if ($payable->status !== 'confirmed') {

                    $payable->update([
                        'payment_status' => 'paid',
                        'status' => 'confirmed'
                    ]);

                    // --- KIRIM EMAIL TIKET ---
                    try {
                        $event = $payable->event;
                        if ($event && $event->confirmation_template_id) {
                            $template = EventEmailTemplate::find($event->confirmation_template_id);
                            if ($template) {
                                Mail::to($payable->email)->send(new DynamicBroadcastMail($template, $payable));
                                Log::info('Ticket email sent via Callback to ' . $payable->email);
                            }
                        }
                    } catch (\Exception $e) {
                        Log::error('Failed sending ticket email via callback: ' . $e->getMessage());
                    }
                    // -------------------------
                }
            } elseif ($payable instanceof ProductOrder) {
                $payable->update([
                    'status' => 'paid'
                ]);
            }
        }

        // Handle Failed (Cancel/Expire)
        if ($newStatus == 'failed') {
            $payable = $transaction->payable;
            if ($payable instanceof Registration) {
                $payable->update(['payment_status' => 'unpaid', 'status' => 'canceled']);
            } elseif ($payable instanceof ProductOrder) {
                $payable->update(['status' => 'canceled']);
            }
        }

        return response()->json(['message' => 'Callback processed']);
    }
}