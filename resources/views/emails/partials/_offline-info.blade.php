<p style="margin: 10px 0;">
    <b style="color: #00554E;">Event Location:</b><br>
    {{ $event->getTranslation('venue', 'id') ?: $event->getTranslation('venue', 'en') }}
</p>