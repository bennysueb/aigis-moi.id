<x-mail::message>
    # Thank You for Attending {{ $event->name }}

    Hello {{ $registration->name }},

    Thank you for your participation in the {{ $event->name }} event.
    We would love to hear your feedback to help us improve future events.

    Please click the button below to fill out the feedback form.

    <x-mail::button :url="route('feedback.show', ['event' => $event->slug, 'registration' => $registration->uuid])">
        Fill Feedback Form
    </x-mail::button>

    Thanks,<br>
    {{ config('app.name') }}
</x-mail::message>