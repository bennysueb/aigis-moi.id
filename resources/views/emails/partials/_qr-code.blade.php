<div style="text-align: center; padding: 20px 0 30px 0;">
    <p style="margin: 0 0 15px 0; color: #333333;">To speed up the check-in process at the venue, please have the QR Code below ready for our staff to scan. You can also access it via the button below.</p>
    {{-- $message->embed() akan menyematkan file dari path dan mengembalikan CID yang benar --}}
    <img src="{{ $message->embed($qrCodeTempPath) }}" alt="QR Code Ticket" style="display: inline-block;">
</div>