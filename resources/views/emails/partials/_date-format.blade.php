@if(!empty($event->daily_schedules))
{{-- Lakukan looping jika ada data jadwal harian yang detail --}}
@foreach($event->daily_schedules as $day)

{{-- Tampilkan Tanggal per Hari --}}
<p style="margin-top: 10px; margin-bottom: 5px; font-size: 16px;">
    <strong>{{ \Carbon\Carbon::parse($day['date'])->locale(app()->getLocale())->translatedFormat('l, d F Y') }}</strong>
</p>

{{-- Tampilkan Sesi Waktu untuk tanggal tersebut --}}
@forelse($day['agenda'] as $session)
<p style="margin: 0; padding-left: 15px; color: #555; font-size: 15px;">
    {{ $session['start_time'] }} - {{ $session['end_time'] }}
</p>
@empty
<p style="margin: 0; padding-left: 15px; color: #777; font-style: italic;">
    (No specific time slots for this day)
</p>
@endforelse

@endforeach
@else
{{--
      BAGIAN FALLBACK: 
      Sebagai pengaman, jika event tidak memiliki jadwal harian (misal, event lama), 
      gunakan format ringkas yang sudah kita buat sebelumnya.
    --}}
@if ($event->start_date->isSameDay($event->end_date))
{{ $event->start_date->locale(app()->getLocale())->translatedFormat('l, d F Y') }}
<br>
{{ $event->start_date->format('H:i') }} - {{ $event->end_date->format('H:i T') }}
@else
@if ($event->start_date->isSameMonth($event->end_date))
{{ $event->start_date->format('d') }} - {{ $event->end_date->locale(app()->getLocale())->translatedFormat('d F Y') }}
@else
{{ $event->start_date->locale(app()->getLocale())->translatedFormat('d F') }} - {{ $event->end_date->locale(app()->getLocale())->translatedFormat('d F Y') }}
@endif
<br>
<span style="font-size: 14px; color: #555;">(Time: {{ $event->start_date->format('H:i T') }}, - {{ $event->end_date->format('H:i T') }})</span>
@endif
@endif