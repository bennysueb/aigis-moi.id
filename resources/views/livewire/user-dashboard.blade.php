<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Event Dashboard') }}
    </h2>
</x-slot>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        {{-- Notifikasi Sukses/Error --}}
        @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
            <span class="block sm:inline">{{ session('message') }}</span>
        </div>
        @endif
        @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse ($allEvents as $event)
            <div class="bg-white rounded-lg shadow-md overflow-hidden flex flex-col" wire:key="event-{{ $event->id }}">

                {{-- Banner Event --}}
                <div class="relative">
                    <img src="{{ $event->getFirstMediaUrl('default', 'card-banner') ?: 'https://via.placeholder.com/400x200' }}" alt="Event Banner" class="w-full h-40 object-cover">

                    {{-- Label Status (Paid/Free) --}}
                    <div class="absolute top-2 right-2">
                        @if($event->is_paid_event)
                        <span class="bg-yellow-500 text-white text-xs font-bold px-2 py-1 rounded shadow">
                            {{ __('dashboard.Paid') }}
                        </span>
                        @else
                        <span class="bg-green-500 text-white text-xs font-bold px-2 py-1 rounded shadow">
                            {{ __('dashboard.Free') }}
                        </span>
                        @endif
                    </div>
                </div>

                <div class="p-6 flex flex-col flex-grow">
                    <h3 class="text-lg font-bold text-gray-900">{{ $event->name }}</h3>

                    {{-- Tanggal Event --}}
                    <p class="text-sm text-gray-600 mt-1">
                        @if($event->start_date->isSameDay($event->end_date))
                        <span>{{ $event->start_date->locale(app()->getLocale())->translatedFormat('l, d F Y') }}</span>
                        @else
                        @if($event->start_date->isSameMonth($event->end_date))
                        <span>{{ $event->start_date->format('d') }} - {{ $event->end_date->locale(app()->getLocale())->translatedFormat('d F Y') }}</span>
                        @else
                        <span>{{ $event->start_date->locale(app()->getLocale())->translatedFormat('d F') }} - {{ $event->end_date->locale(app()->getLocale())->translatedFormat('d F Y') }}</span>
                        @endif
                        @endif
                    </p>

                    {{-- Deskripsi Singkat --}}
                    <p class="text-sm text-gray-500 mt-2 flex-grow">{!! Str::limit(strip_tags($event->description), 200, '...') !!}</p>

                    <div class="mt-4 pt-4 border-t border-gray-200">

                        {{-- Tombol Lihat Detail --}}
                        <a href="{{ route('events.show', $event) }}"
                            class="block w-full mb-2 px-3 py-2 border border-gray-300 font-semibold rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition text-center">
                            {{ __('dashboard.View Details') }}
                        </a>


                        {{-- ====================================================== --}}
                        {{-- ==           LOGIKA TOMBOL AKSI (Updated)           == --}}
                        {{-- ====================================================== --}}

                        @if(isset($myRegistrations[$event->id]))
                        @php
                        $registration = $myRegistrations[$event->id];
                        @endphp

                        {{-- A. SUDAH TERDAFTAR --}}
                        @if($registration->attendance_type === 'online')
                        {{-- Case A1: Online (Join Meeting) --}}
                        <a href="{{ $event->meeting_link }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="block w-full text-center px-4 py-2 bg-green-600 text-white font-semibold rounded-md hover:bg-green-700 transition">
                            {{ __('button.Join_Meeting') }}
                        </a>
                        @else
                        {{-- Case A2: Offline (Lihat Tiket) --}}
                        <a href="{{ route('tickets.qrcode', $registration->uuid) }}"
                            class="block w-full text-center px-4 py-2 bg-indigo-600 text-white font-semibold rounded-md hover:bg-indigo-700 transition">
                            {{ __('button.view_ticket') }}
                        </a>
                        @endif

                        @elseif($event->quota > 0 && $event->registrations_count >= $event->quota)
                        {{-- B. KUOTA HABIS --}}
                        <button disabled class="w-full px-4 py-2 bg-red-100 text-red-500 font-semibold rounded-md cursor-not-allowed transition">
                            Kuota Penuh
                        </button>

                        @else
                        {{-- C. BELUM DAFTAR (Siap Join) --}}

                        @if($event->is_paid_event)
                        {{-- Case C1: Event BERBAYAR -> Redirect ke Halaman Beli --}}
                        <a href="{{ route('event.register', $event->slug) }}"
                            class="block w-full text-center px-4 py-2 bg-blue-600 text-white font-semibold rounded-md hover:bg-blue-700 transition">
                            Beli Tiket
                        </a>
                        @else
                        {{-- Case C2: Event GRATIS -> Quick Join Modal (Logic Lama) --}}
                        <button wire:click="joinEvent({{ $event->id }})"
                            wire:loading.attr="disabled"
                            class="w-full px-4 py-2 bg-indigo-600 text-white font-semibold rounded-md hover:bg-indigo-700 transition disabled:opacity-50">
                            {{ __('button.Join_Event') }}
                        </button>
                        @endif

                        @endif
                    </div>
                </div>
            </div>
            @empty
            <p class="col-span-1 md:col-span-2 lg:col-span-3 text-center text-gray-500 py-12">
                {{ __('dashboard.There are no upcoming events at the moment') }}
            </p>
            @endforelse
        </div>

        <div class="mt-16">
            <h3 class="text-2xl font-bold text-gray-800 mb-6">{{ __('dashboard.Post_Event_Activities') }}</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse ($pastEvents as $event)
                <div class="bg-white rounded-lg shadow-md overflow-hidden flex flex-col" wire:key="past-event-{{ $event->id }}">
                    <img src="{{ $event->getFirstMediaUrl('default', 'card-banner') ?: 'https://via.placeholder.com/400x200' }}" alt="Event Banner" class="w-full h-40 object-cover">
                    <div class="p-6 flex flex-col flex-grow">
                        <h3 class="text-lg font-bold text-gray-900">{{ $event->name }}</h3>
                        <p class="text-sm text-gray-600 mt-1">
                            <span class="font-semibold">Held on:</span> {{ $event->start_date->format('d M Y') }}
                        </p>
                        <p class="text-sm text-gray-500 mt-2 flex-grow">{{ Str::limit($event->description, 100) }}</p>

                        <div class="mt-4 pt-4 border-t border-gray-200">
                            {{-- Tombol untuk acara lampau --}}
                            <a href="{{ route('events.show', $event->slug) }}"
                                class="block w-full text-center px-4 py-2 bg-gray-500 text-white font-semibold rounded-md hover:bg-gray-600">
                                {{ __('dashboard.View Details') }}
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <p class="col-span-3 text-center text-gray-500">{{ __('dashboard.You have not attended any past events') }}</p>
                @endforelse
            </div>
        </div>

        {{-- Modal untuk Form Kustom --}}
        @if($showCustomFieldsModal && $eventToRegister)
        <div class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center z-50 px-4 py-6 sm:py-10">
            <div class="bg-white rounded-lg shadow-xl p-8 w-full max-w-xl">
                <h3 class="text-xl font-bold mb-2">{{ __('dashboard.Additional_Registration_Form') }}</h3>
                <p class="text-sm text-gray-600 mb-6">{{ __('dashboard.Please fill in the following details for the event') }} "{{ $eventToRegister->name }}".</p>

                <form wire:submit.prevent="submitCustomFields">
                    <div class="space-y-4 max-h-96 overflow-y-auto pr-4">
                        @foreach($eventToRegister->inquiryForm->fields as $field)

                        @php
                        $fieldType = $field['type'];
                        @endphp

                        @if($fieldType === 'heading')
                        <div class="pt-4">
                            <h3 class="text-xl font-bold mb-2 text-gray-800">{{ $field['label'] }}</h3>
                        </div>

                        {{-- 2. Tampilkan sebagai Paragraph --}}
                        @elseif($fieldType === 'paragraph')
                        <p class="text-sm text-gray-600">{{ $field['label'] }}</p>

                        {{-- 3. Untuk semua tipe lainnya (yang merupakan input field) --}}
                        @else
                        <div>
                            <label for="field-{{ $field['name'] }}" class="block text-sm font-medium text-gray-700">
                                {{ $field['label'] }}
                                @if($field['required']) <span class="text-red-500">*</span> @endif
                            </label>

                            @switch($field['type'])
                            @case('select')
                            <select id="field-{{ $field['name'] }}"
                                wire:model="customFormData.{{ $field['name'] }}"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">Choose one...</option>
                                {{-- PERBAIKAN: Langsung gunakan $option karena isinya string --}}
                                @foreach($field['options'] as $option)
                                <option value="{{ $option }}">{{ $option }}</option>
                                @endforeach
                            </select>
                            @break

                            @case('radio')
                            <div class="mt-2 space-y-2">
                                {{-- PERBAIKAN: Langsung gunakan $option karena isinya string --}}
                                @foreach($field['options'] as $option)
                                <label class="flex items-center">
                                    <input type="radio"
                                        wire:model="customFormData.{{ $field['name'] }}"
                                        value="{{ $option }}"
                                        class="h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                                    <span class="ml-2 text-sm text-gray-700">{{ $option }}</span>
                                </label>
                                @endforeach
                            </div>
                            @break

                            @case('checkbox-multiple')
                            <div
                                wire:ignore
                                x-data="{
                                    options: @js($field['options']),
                                    selected: @entangle('customFormData.' . $field['name'])
                                }"
                                x-init="
                                    $nextTick(() => {
                                        new TomSelect($refs.select, {
                                            options: options.map(option => ({ value: option, text: option })),
                                            items: selected,
                                            plugins: ['checkbox_options', 'remove_button'],
                                            placeholder: 'Choose one or more',
                                            onChange: (values) => {
                                                selected = values;
                                            }
                                        });
                                    })
                                ">

                                @if(!in_array($field['type'], ['checkbox', 'checkbox-multiple'])) <label for="field-{{ $field['name'] }}" class="block text-sm font-medium text-gray-700">
                                    {{ $field['label'] }}
                                    @if($field['required']) <span class="text-red-500">*</span> @endif
                                </label>
                                @endif
                                <select x-ref="select" id="field-{{ $field['name'] }}" multiple class="mt-1"></select>
                            </div>
                            @break

                            @case('checkbox')
                            {{-- PERBAIKAN: Menangani checkbox tunggal (tanpa options) dan grup --}}
                            @if(empty($field['options']))
                            {{-- Ini untuk checkbox tunggal seperti "Saya Setuju" --}}
                            <label class="flex items-center mt-2">
                                <input type="checkbox"
                                    wire:model="customFormData.{{ $field['name'] }}"
                                    value="true"
                                    class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-700">Yes, I agree</span>
                            </label>
                            @else
                            {{-- Ini untuk checkbox grup (jika nanti ada) --}}
                            <div class="mt-2 space-y-2">
                                @foreach($field['options'] as $option)
                                <label class="flex items-center">
                                    <input type="checkbox"
                                        wire:model="customFormData.{{ $field['name'] }}"
                                        value="{{ $option }}"
                                        class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                    <span class="ml-2 text-sm text-gray-700">{{ $option }}</span>
                                </label>
                                @endforeach
                            </div>
                            @endif
                            @break

                            @default
                            <input type="{{ $field['type'] }}"
                                id="field-{{ $field['name'] }}"
                                wire:model="customFormData.{{ $field['name'] }}"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                placeholder="{{ $field['placeholder'] ?? '' }}">
                            @endswitch

                            @error('customFormData.' . $field['name']) <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        @endif
                        @endforeach
                    </div>

                    <div class="mt-8 flex justify-end space-x-4">
                        {{-- Tombol Batal tidak berubah --}}
                        <button type="button" wire:click="$set('showCustomFieldsModal', false)" class="px-4 py-2 bg-gray-200 text-gray-800 font-semibold rounded-md hover:bg-gray-300">
                            {{ __('button.cancel') }}
                        </button>

                        {{-- Tombol Submit dengan Loading State --}}
                        <button type="submit"
                            wire:loading.attr="disabled"
                            wire:target="submitCustomFields"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-semibold rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed">

                            {{-- Ikon Spinner yang muncul saat loading --}}
                            <svg wire:loading wire:target="submitCustomFields" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>

                            {{-- Teks yang berubah saat loading --}}
                            <span wire:loading.remove wire:target="submitCustomFields">
                                {{ __('button.register_now') }}
                            </span>
                            <span wire:loading wire:target="submitCustomFields">
                                {{ __('auth.loading') }}
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif


        @if($showAttendanceTypeModal && $eventForAttendanceChoice)
        <div class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-xl p-8 w-full max-w-md">
                <h3 class="text-xl font-bold mb-4">Choose Attendance Type</h3>
                <p class="text-sm text-gray-600 mb-6">How would you like to attend the event "{{ $eventForAttendanceChoice->name }}"?</p>

                <form wire:submit.prevent="submitAttendanceType">
                    <div class="space-y-4">
                        <label for="type-online" class="flex items-center p-4 border rounded-md cursor-pointer hover:bg-gray-50">
                            <input type="radio" id="type-online" wire:model="selectedAttendanceType" value="online" class="h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                            <span class="ml-3 text-sm font-medium text-gray-700">Attend Online</span>
                        </label>
                        <label for="type-offline" class="flex items-center p-4 border rounded-md cursor-pointer hover:bg-gray-50">
                            <input type="radio" id="type-offline" wire:model="selectedAttendanceType" value="offline" class="h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                            <span class="ml-3 text-sm font-medium text-gray-700">Attend Offline (In-Person)</span>
                        </label>
                    </div>

                    <div class="mt-8 flex justify-end space-x-4">
                        <button type="button" wire:click="$set('showAttendanceTypeModal', false)" class="px-4 py-2 bg-gray-200 text-gray-800 font-semibold rounded-md hover:bg-gray-300">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white font-semibold rounded-md hover:bg-blue-700">
                            Next
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif

    </div>
</div>