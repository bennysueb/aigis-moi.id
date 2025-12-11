<div style="padding: 15px; border: 1px solid #eee; border-radius: 5px; margin-top: 15px; font-size: 14px; line-height: 1.5;">
    <b style="color: #00554E;">Online Event Information:</b><br>
    <b>Platform:</b> {{ $event->platform === 'Lainnya...' ? ($event->meeting_info['platform_name'] ?? 'N/A') : $event->platform }}<br>
    @if($event->meeting_link)
    <b>Link:</b> <a href="{{ $event->meeting_link }}" target="_blank" style="color: #007BFF;">Click to Join</a><br>
    @endif
    @if ($event->platform === 'Zoom Meeting')
    <b>Meeting ID:</b> {{ $event->meeting_info['meeting_id'] ?? '-' }}<br>
    <b>Passcode:</b> {{ $event->meeting_info['passcode'] ?? '-' }}<br>
    @elseif ($event->platform === 'Lainnya...')
    <b>Petunjuk:</b> {{ $event->meeting_info['instructions'] ?? '-' }}
    @endif
</div>