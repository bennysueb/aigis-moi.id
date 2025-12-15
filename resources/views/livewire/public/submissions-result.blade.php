<div>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Submissions for: {{ $form->name }}
            </h2>

            <a href="{{ route('forms.results.export', $form->slug) }}"
                class="inline-flex items-center bg-green-600 hover:bg-green-700 text-greener font-bold py-2 px-4 rounded text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 mr-2">
                    <path d="M10.75 2.75a.75.75 0 0 0-1.5 0v8.614L6.295 8.235a.75.75 0 1 0-1.09 1.03l4.25 4.5a.75.75 0 0 0 1.09 0l4.25-4.5a.75.75 0 0 0-1.09-1.03l-2.955 3.129V2.75Z" />
                    <path d="M3.5 12.75a.75.75 0 0 0-1.5 0v2.5A2.75 2.75 0 0 0 4.75 18h10.5A2.75 2.75 0 0 0 18 15.25v-2.5a.75.75 0 0 0-1.5 0v2.5c0 .69-.56 1.25-1.25 1.25H4.75c-.69 0-1.25-.56-1.25-1.25v-2.5Z" />
                </svg>
                <span>Ekspor ke Excel</span>
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @forelse($submissions as $submission)
                    <div class="border rounded-lg p-4 mb-4">
                        <p class="text-sm text-gray-600">
                            Submitted on: {{ $submission->created_at->setTimezone('Asia/Jakarta')->format('d M Y, H:i T') }}
                        </p>
                        <hr class="my-2">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-6 mt-4">

                            {{-- ▼▼▼ LOGIKA BARU UNTUK MENAMPILKAN SEMUA FIELD SECARA DINAMIS ▼▼▼ --}}
                            @foreach($form->fields as $field)
                                @php
                                    $fieldName = $field['name'];
                                    $fieldType = $field['type'];
                                @endphp

                                {{-- Kondisi untuk Tipe Field yang Berbeda --}}
                                @if($fieldType === 'heading')
                                    <div class="md:col-span-2 mt-2">
                                        <h4 class="text-lg font-bold">{{ $field['label'] }}</h4>
                                    </div>
                                @elseif($fieldType === 'paragraph')
                                    <div class="md:col-span-2">
                                        <p class="text-gray-600">{{ $field['label'] }}</p>
                                    </div>
                                @elseif($fieldType === 'image')
                                    <div class="break-words">
                                        <strong class="font-medium capitalize">{{ $field['label'] }}:</strong>
                                        @php $media = $submission->getFirstMedia($fieldName); @endphp
                                        @if($media)
                                            <div class="mt-2">
                                                <a href="{{ $media->getUrl() }}" target="_blank">
                                                    <img src="{{ $media->getUrl() }}" alt="{{ $media->name }}" class="max-w-xs rounded border">
                                                </a>
                                                <p class="text-xs text-gray-500 mt-1">{{ $media->name }} ({{ $media->human_readable_size }})</p>
                                            </div>
                                        @else
                                            <p>-</p>
                                        @endif
                                    </div>
                                @elseif(in_array($fieldType, ['file', 'signature']))
                                     <div class="break-words">
                                        <strong class="font-medium capitalize">{{ $field['label'] }}:</strong>
                                        @php $media = $submission->getFirstMedia($fieldName); @endphp
                                        @if($media)
                                             <p class="mt-1">
                                                <a href="{{ $media->getUrl() }}" target="_blank" class="text-blue-600 hover:underline">
                                                    {{ $media->name }} ({{ $media->human_readable_size }})
                                                </a>
                                            </p>
                                        @else
                                            <p>-</p>
                                        @endif
                                    </div>
                                @else
                                    {{-- Ini untuk field input teks biasa (text, email, select, checkbox, dll) --}}
                                    <div class="break-words">
                                        <strong class="font-medium capitalize">{{ $field['label'] }}:</strong>
                                        <p>
                                            {{-- Cek apakah datanya ada --}}
                                            @if(isset($submission->data[$fieldName]))
                                            
                                                {{-- Cek apakah datanya Array (checkbox/multi-select) --}}
                                                @if(is_array($submission->data[$fieldName]))
                                                    {{-- GABUNGKAN array jadi string dengan koma --}}
                                                    {{ implode(', ', $submission->data[$fieldName]) }}
                                                @else
                                                    {{-- Tampilkan teks biasa --}}
                                                    {{ $submission->data[$fieldName] }}
                                                @endif
                                                
                                            @else
                                                -
                                            @endif
                                        </p>
                                    </div>
                                @endif
                            @endforeach
                            {{-- ▲▲▲ LOGIKA BARU SELESAI DI SINI ▲▲▲ --}}
                        </div>
                    </div>
                    @empty
                    <p class="text-center text-gray-500">No submissions yet for this form.</p>
                    @endforelse

                    <div class="mt-4">
                        {{ $submissions->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>