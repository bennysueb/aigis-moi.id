<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use App\Models\Registration;
use App\Models\EventEmailTemplate;
use App\Mail\DynamicBroadcastMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Models\User;

class InvitationController extends Controller
{
    /**
     * Menampilkan form konfirmasi kehadiran.
     */
    public function show(Invitation $invitation)
    {
        // Jika sudah pernah respon, arahkan ke halaman info
        if ($invitation->status !== 'pending') {
            return view('invitation.already-responded', [
                'invitation' => $invitation,
                'event' => $invitation->event
            ]);
        }

        return view('invitation.confirm', [
            'invitation' => $invitation,
            'event' => $invitation->event
        ]);
    }

    /**
     * Menampilkan Surat Undangan Digital (E-Letter)
     */
    public function letter(Invitation $invitation)
    {
        $event = $invitation->event;
        $content = $event->invitation_letter_body;

        if (empty($content)) {
            return redirect()->route('invitation.confirm', $invitation->uuid);
        }

        $confirmLink = route('invitation.confirm', $invitation->uuid);
        $letterLink = route('invitation.letter', $invitation->uuid);

        // Styling Tombol untuk di dalam surat
        $confirmButton = '<a href="' . $confirmLink . '" style="display:inline-block;background:#2563eb;color:#fff;padding:8px 16px;text-decoration:none;border-radius:4px;font-weight:bold;margin-top:5px;">Konfirmasi Kehadiran</a>';

        $replacements = [
            '{name}'            => $invitation->name,
            '{company}'         => $invitation->company ?? '-',
            '{category}'        => $invitation->category ?? '-',
            '{event_name}'      => $event->name,
            '{link_surat}'      => $letterLink,    // URL biasa
            '{link_konfirmasi}' => $confirmButton, // Tombol CTA
        ];

        $processedContent = str_replace(array_keys($replacements), array_values($replacements), $content);

        return view('invitation.letter', [
            'invitation' => $invitation,
            'event' => $event,
            'content' => $processedContent,
        ]);
    }

    /**
     * Memproses data konfirmasi.
     */
    public function submit(Request $request, Invitation $invitation)
    {
        // Validasi input dasar
        $request->validate([
            'response_status' => 'required|in:confirmed,represented,declined',
        ]);

        DB::beginTransaction();
        try {
            // 1. Update Status Undangan di tabel 'invitations'
            $invitation->status = $request->response_status;
            $invitation->responded_at = now();

            // Siapkan variabel data calon pendaftar (Default pakai data undangan)
            $regName = $invitation->name;
            $regEmail = $invitation->email;
            $regPhone = $invitation->phone_number;
            $regData = [
                'nama_instansi' => $invitation->company,
                'tipe_instansi' => 'Invited Guest', // Flag khusus
                'jabatan' => $invitation->category ?? 'Guest',
                'source' => 'Invitation System'
            ];
            $attendanceType = $invitation->event->type; // Default ikut tipe event

            // 2. Logika Berdasarkan Pilihan User
            if ($request->response_status === 'confirmed') {
                // Validasi jika user mengupdate data diri sendiri
                $request->validate([
                    'name' => 'required|string',
                    'email' => 'required|email',
                    'phone' => 'required|string',
                ]);

                $regName = $request->name;
                $regEmail = $request->email;
                $regPhone = $request->phone;
                $regData['jabatan'] = $request->jabatan ?? $regData['jabatan'];

                // Jika event Hybrid, user harus pilih online/offline
                if ($invitation->event->type === 'hybrid') {
                    $request->validate(['attendance_type' => 'required|in:offline,online']);
                    $attendanceType = $request->attendance_type;
                }
            } elseif ($request->response_status === 'represented') {
                // Validasi data perwakilan
                $request->validate([
                    'rep_name' => 'required|string',
                    'rep_email' => 'required|email',
                    'rep_phone' => 'required|string',
                ]);

                $regName = $request->rep_name;
                $regEmail = $request->rep_email;
                $regPhone = $request->rep_phone;
                $regData['jabatan'] = $request->rep_jabatan ?? 'Representative';

                $regData['representing'] = $invitation->name;

                // Simpan data wakil di tabel invitation sebagai history
                $invitation->representative_data = [
                    'name' => $regName,
                    'email' => $regEmail,
                    'phone' => $regPhone,
                    'jabatan' => $regData['jabatan']
                ];

                if ($invitation->event->type === 'hybrid') {
                    $request->validate(['attendance_type' => 'required|in:offline,online']);
                    $attendanceType = $request->attendance_type;
                }
            } elseif ($request->response_status === 'declined') {
                // Jika menolak, cukup simpan alasan dan selesai
                $invitation->rejection_reason = $request->rejection_reason;
                $invitation->save();
                DB::commit();

                return redirect()->route('home')->with('status', 'Terima kasih atas konfirmasi Anda. Kami menyayangkan ketidakhadiran Anda.');
            }

            // Simpan perubahan pada invitation
            $invitation->save();
            $existingUser = User::where('email', $regEmail)->first();
            $userId = $existingUser ? $existingUser->id : null;

            // 3. Buat data Registration (Agar dapat QR Code & Tiket)
            // Kita pakai try-catch khusus di sini untuk menangani duplikat email di event yg sama
            $registration = Registration::updateOrCreate(
                [
                    'event_id' => $invitation->event_id,
                    'email' => $regEmail, // Unik per event
                ],
                [
                    'name' => $regName,
                    'phone_number' => $regPhone,
                    'attendance_type' => $attendanceType,
                    'data' => $regData,
                    'user_id' => $userId,
                ]
            );

            // 4. Kirim Email Tiket Resmi (Menggunakan fitur yang sudah ada)
            if ($invitation->event->confirmation_template_id) {
                $template = EventEmailTemplate::find($invitation->event->confirmation_template_id);
                if ($template) {
                    // Menggunakan antrian agar loading user tidak lama
                    Mail::to($registration->email)->queue(new DynamicBroadcastMail($template, $registration));
                }
            }

            DB::commit();

            // 5. Redirect ke Halaman Sukses yang sudah ada (menampilkan QR Code)
            return redirect()->route('events.register.success', [
                'event' => $invitation->event->slug,
                'registration' => $registration->uuid
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat memproses data: ' . $e->getMessage())->withInput();
        }
    }
}
