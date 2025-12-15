<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $form->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900">
                    @if($success)
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4" role="alert">
                        <p class="font-bold">Submission Successful!</p>
                        <p>Thank you, your form has been submitted.</p>
                    </div>
                    @else
                    <form wire:submit.prevent="submit">
                        <div class="space-y-6" x-data>
                            {{-- Loop untuk membuat field form secara dinamis --}}
                            @foreach($form->fields as $field)
                            @php
                            $fieldName = $field['name'];
                            $fieldType = $field['type'];
                            @endphp

                            {{-- 1. Tampilkan sebagai Heading --}}
                            @if($fieldType === 'heading')
                            <div class="pt-4 pb-2">
                                <h3 class="text-xl font-bold text-gray-800">{{ $field['label'] }}</h3>
                            </div>

                            {{-- 2. Tampilkan sebagai Paragraph --}}
                            @elseif($fieldType === 'paragraph')
                            <p class="text-sm text-gray-600">{{ $field['label'] }}</p>

                            {{-- 3. Tampilkan sebagai field input (semua tipe lainnya) --}}
                            @else
                            <div>
                                {{-- Jangan tampilkan label untuk checkbox karena sudah ada di sampingnya --}}
                                @if (!in_array($fieldType, ['checkbox', 'checkbox-multiple']))
                                <label for="{{ $fieldName }}" class="block text-sm font-medium text-gray-700">
                                    {{ $field['label'] }}
                                    @if($field['required']) <span class="text-red-500">*</span> @endif
                                </label>
                                @endif

                                {{-- Logika untuk berbagai tipe input --}}
                                @if($fieldType === 'textarea')
                                <textarea wire:model.defer="formData.{{ $fieldName }}" id="{{ $fieldName }}" rows="4" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></textarea>

                                @elseif($fieldType === 'select')
                                @php
                                $availableOptions = array_diff($field['options'], $bookedSlots);
                                $isFullyBooked = !empty($field['enable_slot_validation']) && count($availableOptions) === 0;
                                @endphp
                                <select wire:model.defer="formData.{{ $fieldName }}" id="{{ $fieldName }}" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm sm:text-sm @if($isFullyBooked) bg-gray-100 cursor-not-allowed @endif" @if($isFullyBooked) disabled @endif>
                                    @if($isFullyBooked)
                                    <option value="">Semua pilihan sudah terisi</option>
                                    @else
                                    <option value="">Choose one...</option>
                                    @foreach($field['options'] as $option)
                                    @php $isBooked = !empty($field['enable_slot_validation']) && in_array($option, $bookedSlots); @endphp
                                    <option value="{{ $option }}" @if($isBooked) disabled class="text-gray-400" @endif>
                                        {{ $option }} @if($isBooked) (Tidak tersedia) @endif
                                    </option>
                                    @endforeach
                                    @endif
                                </select>

                                @elseif($fieldType === 'radio')
                                <div class="mt-2 space-y-2">
                                    @foreach($field['options'] as $option)
                                    @php $isBooked = !empty($field['enable_slot_validation']) && in_array($option, $bookedSlots); @endphp
                                    <label class="inline-flex items-center">
                                        <input type="radio" wire:model.defer="formData.{{ $fieldName }}" value="{{ $option }}" class="rounded-full border-gray-300 text-indigo-600 shadow-sm" @if($isBooked) disabled @endif>
                                        <span class="ml-2 text-sm @if($isBooked) text-gray-400 line-through @endif">{{ $option }}</span>
                                    </label>
                                    @endforeach
                                </div>
                                
                                @elseif($fieldType === 'checkbox-multiple')
                                <div class="mt-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        {{ $field['label'] }} @if($field['required']) <span class="text-red-500">*</span> @endif
                                    </label>
                                    
                                    <div class="grid grid-cols-1 gap-2">
                                        @foreach($field['options'] as $option)
                                            <label class="inline-flex items-center">
                                                {{-- 
                                                   PENTING: 
                                                   1. wire:model.defer mengarah ke array di controller.
                                                   2. value="{{ $option }}" WAJIB ada agar Livewire tahu nilai spesifik ini.
                                                --}}
                                                <input type="checkbox" 
                                                       wire:model.defer="formData.{{ $fieldName }}" 
                                                       value="{{ $option }}" 
                                                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                                <span class="ml-2 text-gray-700">{{ $option }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                    
                                    {{-- Pesan Error --}}
                                    @error('formData.' . $fieldName) 
                                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span> 
                                    @enderror
                                </div>

                                @elseif($fieldType === 'checkbox')
                                <div class="mt-2">
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" wire:model.defer="formData.{{ $fieldName }}" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm">
                                        <span class="ml-2 text-sm">{{ $field['label'] }} @if($field['required']) <span class="text-red-500">*</span> @endif</span>
                                    </label>
                                </div>

                                @elseif(in_array($fieldType, ['file', 'image']))
                                <input type="file" wire:model="formData.{{ $fieldName }}" id="{{ $fieldName }}" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                <div wire:loading wire:target="formData.{{ $fieldName }}">Uploading...</div>

                                @elseif($fieldType === 'signature')
                                <div class="mt-1">
                                    <x-signature-pad wire:model="formData.{{ $fieldName }}" id="{{ $fieldName }}"></x-signature-pad>
                                </div>

                                @else
                                {{-- Input default untuk text, email, number, dll. --}}
                                <input type="{{ $fieldType }}" wire:model.defer="formData.{{ $fieldName }}" id="{{ $fieldName }}" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                @endif

                                @error('formData.' . $fieldName) <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                            @endif
                            @endforeach

                            <div>
                                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                    Submit
                                </button>
                            </div>
                        </div>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>