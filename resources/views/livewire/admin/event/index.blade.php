<div>
    <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-900">
                Event Management
            </h1>
            <x-primary-button wire:click="create">Create New Event</x-primary-button>
        </div>
        <div class="mt-6">
            <x-text-input wire:model.live.debounce.300ms="search" placeholder="Search events by name..." class="w-full md:w-1/3" />
        </div>
    </div>

    <div class="bg-gray-200 bg-opacity-25 p-6 lg:p-8 mt-6 overflow-x-auto">
        @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('message') }}</span>
        </div>
        @endif

        <table class="min-w-full divide-y divide-gray-200 responsive-table">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name (EN)</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quota</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Registrants
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Report
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Feedback Control</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($events as $event)
                <tr>
                    <td class="px-6 py-4 data-label="Name (EN)">{{ $event->getTranslation('name', 'en') }}</td>
                    <td data-label="Date">{{ $event->start_date->format('d M Y, H:i') }}</td>
                    <td data-label="Quota">{{ $event->quota }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <a href="{{ route('admin.events.registrants', $event) }}" class="text-blue-600 hover:text-blue-900 hover:underline">
                            {{ $event->registrations_count }} / {{ $event->quota > 0 ? $event->quota : '∞' }}
                        </a>
                    </td>

                    <td class="px-6 py-4 whitespace-nowrap" data-label="Report">
                        <a href="{{ route('admin.events.report', $event) }}"
                            class="inline-flex items-center px-3 py-1 bg-gray-200 text-gray-700 rounded-full text-xs font-semibold hover:bg-gray-300"
                            wire:navigate>
                            Lihat Report
                        </a>
                    </td>

                    <td data-label="Status">
                        <span class="...">{{ $event->is_active ? 'Active' : 'Inactive' }}</span>
                    </td>

                    <td data-label="Feedback Control" class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center space-x-3">
                            <label for="feedback-toggle-{{ $event->id }}" class="flex items-center cursor-pointer">
                                <div class="relative">
                                    <input type="checkbox" id="feedback-toggle-{{ $event->id }}" class="sr-only"
                                        wire:click="toggleFeedbackStatus({{ $event->id }})"
                                        {{ $event->is_feedback_active ? 'checked' : '' }}>
                                    <div class="block bg-gray-600 w-10 h-6 rounded-full"></div>
                                    <div class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition"></div>
                                </div>
                            </label>
                            @if($event->is_feedback_active)
                            <div class="flex flex-col">
                                <button wire:click="openFeedbackFormModal({{ $event->id }})" class="text-xs text-blue-600 hover:text-blue-800 hover:underline text-left">
                                    {{ $event->feedbackForm ? 'Change Form' : 'Choose Form' }}
                                </button>
                                <span class="text-xs text-gray-500 mt-1" title="{{ $event->feedbackForm->name ?? 'No form selected' }}">
                                    {{ $event->feedbackForm ? Str::limit($event->feedbackForm->name, 20) : 'Not selected' }}
                                </span>
                            </div>
                            @endif
                        </div>
                    </td>

                    <td data-label="Actions">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                    <div>Actions</div>
                                    <div class="ms-1">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                @if($event->is_paid_event)
                                <button wire:click="manageTickets({{ $event->id }})" class="text-green-600 hover:text-green-900 hover:bg-gray-100 mt-1 mr-3 flex items-center w-full text-start block px-4 py-2 text-sm leading-5  focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out" title="Manage Tickets">
                                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                                    </svg>
                                    Tickets
                                </button>
                                @endif
                                <x-dropdown-link :href="route('admin.events.email-templates', $event)" wire:navigate>
                                    {{ __('Email Templates') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('admin.checkin.camera', $event)" wire:navigate>
                                    {{ __('Scan with Camera') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('admin.checkin.handheld', $event)" wire:navigate>
                                    {{ __('Scan with Handheld') }}
                                </x-dropdown-link>

                                <div class="border-t border-gray-100 my-1"></div>
                                <x-dropdown-link :href="route('admin.checkin.register-rfid', $event)" wire:navigate>
                                    {{ __('Register RFID Card') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('admin.checkin.rfid-tap', $event)" wire:navigate>
                                    {{ __('RFID Fast Check-in') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('admin.checkin.return-by-qr', $event)" wire:navigate>
                                    {{ __('RFID Return by QR') }}
                                </x-dropdown-link>

                                <div class="border-t border-gray-100"></div>

                                <x-dropdown-link :href="route('admin.events.registrants', $event)" wire:navigate>
                                    {{ __('View Registrants') }}
                                </x-dropdown-link>
                                <button wire:click="edit({{ $event->id }})" class="w-full text-start block px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out">
                                    {{ __('Edit') }}
                                </button>

                                <x-dropdown-link :href="route('admin.events.invitations', $event)" wire:navigate>
                                    {{ __('Manage Invitations') }}
                                </x-dropdown-link>

                                <div class="border-t border-gray-100"></div>

                                <button wire:click="delete({{ $event->id }})" onclick="return confirm('Are you sure?')" class="w-full text-start block px-4 py-2 text-sm leading-5 text-red-600 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out">
                                    {{ __('Delete') }}
                                </button>

                                @if($event->feedback_form_id && $event->is_feedback_active)
                                <div class="border-t border-gray-100"></div>
                                <x-dropdown-link :href="route('feedback.results.show', $event)" target="_blank">
                                    View Feedback Results
                                </x-dropdown-link>
                                @endif

                                @if($event->type === 'online' || $event->type === 'hybrid')
                                <div x-data="{ copied: false }" class="inline-block w-full">
                                    <button
                                        @click="
                                                        navigator.clipboard.writeText('{{ route('online.checkin.show', $event) }}');
                                                        copied = true;
                                                        setTimeout(() => { copied = false }, 2000);
                                                    "
                                        class="text-green-600 hover:text-green-900 relative hover:bg-gray-100 mt-1 mr-3 flex items-center w-full text-start block px-4 py-2 text-sm leading-5  focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out">
                                        <span x-show="!copied">Copy Check-in Link</span>
                                        <span x-show="copied" class="text-green-500 font-bold">Copied!</span>
                                    </button>
                                </div>
                                @endif
                            </x-slot>
                        </x-dropdown>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">No events found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <style>
            input:checked~.dot {
                transform: translateX(100%);
                background-color: #3b82f6;
                /* Warna biru saat aktif */
            }

            input:checked~.block {
                background-color: #9ca3af;
                /* Warna latar belakang saat aktif */
            }
        </style>
    </div>
    <div class="mt-4 px-6 lg:px-8">
        {{ $events->links() }}
    </div>


    @if($showFeedbackModal)
    <div class="fixed z-50 inset-0 overflow-y-auto livewire-persistent-modal">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity">
                <div class="absolute inset-0 transition-opacity">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form wire:submit.prevent="assignFeedbackForm">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Assign Feedback Form
                            </h3>
                            <div class="mt-4">
                                <label for="feedback_form_id_to_assign" class="block text-sm font-medium text-gray-700">Select a form to use for this event</label>
                                <select wire:model.defer="feedback_form_id_to_assign" id="feedback_form_id_to_assign" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                    <option value="">-- Please Select --</option>
                                    @foreach($allFeedbackForms as $form)
                                    <option value="{{ $form->id }}">{{ $form->name }}</option>
                                    @endforeach
                                </select>
                                @error('feedback_form_id_to_assign') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 sm:ml-3 sm:w-auto sm:text-sm">
                                Assign Form
                            </button>
                            <button type="button" wire:click="closeFeedbackFormModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif


        {{-- Modal Form --}}
        @if($showModal)
        <div class="fixed z-50 inset-0 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                    <form wire:submit.prevent="save">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">{{ $isEditMode ? 'Edit Event' : 'Create Event' }}</h3>

                            {{-- Baris 1: Nama Event --}}
                            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div><label class="block text-sm font-medium text-gray-700">Name (EN)</label><input type="text" wire:model.defer="name_en" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></div>
                                <div><label class="block text-sm font-medium text-gray-700">Name (ID)</label><input type="text" wire:model.defer="name_id" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></div>
                            </div>

                            {{-- Baris 2: Tema Event --}}
                            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div><label class="block text-sm font-medium text-gray-700">Theme (EN)</label><input type="text" wire:model.defer="theme_en" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></div>
                                <div><label class="block text-sm font-medium text-gray-700">Theme (ID)</label><input type="text" wire:model.defer="theme_id" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></div>
                            </div>

                            <div class="mt-4 border-t pt-4 border-gray-100">
                                <label for="use_external_link" class="flex items-center cursor-pointer">
                                    <div class="relative">
                                        <input type="checkbox" id="use_external_link" wire:model.live="use_external_link" class="sr-only peer">
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                    </div>
                                    <span class="ml-3 text-sm font-medium text-gray-900">Gunakan Link Pendaftaran Eksternal</span>
                                </label>
                                <p class="text-xs text-gray-500 mt-1 ml-14">Jika aktif, pendaftaran di web ini akan dimatikan dan dialihkan ke link tujuan.</p>
                            </div>

                            <div class="mt-4 ml-14" x-show="$wire.use_external_link" x-transition>
                                <x-input-label for="external_registration_link" value="Link Eksternal (URL)" />
                                <x-text-input id="external_registration_link" type="url" class="mt-1 block w-full"
                                    wire:model="external_registration_link"
                                    placeholder="https://googleform.com/..." />
                                <x-input-error :messages="$errors->get('external_registration_link')" class="mt-2" />
                            </div>

                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-700">Registration Requirement</label>
                                <div class="flex items-center space-x-4 mt-2 p-3 bg-gray-50 rounded-md border">
                                    <label class="flex items-center cursor-pointer">
                                        <input type="radio" wire:model="requires_account" name="requires_account_option" value="0">
                                        <span class="ml-2 text-gray-700">Guest Registration (Default)</span>
                                    </label>
                                    <label class="flex items-center cursor-pointer">
                                        <input type="radio" wire:model="requires_account" name="requires_account_option" value="1">
                                        <span class="ml-2 text-gray-700">Account Required</span>
                                    </label>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Choose if attendees must log in/create an account to register for this event.</p>
                            </div>

                            {{-- Baris 9: Personnel (Speakers & Moderators) --}}
                            <div class="mt-6 border-t pt-4">
                                <h4 class="text-md font-medium text-gray-900 mb-4">Personnel</h4>
                                
                                {{-- Speaker Section --}}
                                <div class="mt-2 p-4 border rounded-md bg-white">
                                    <h5 class="font-semibold text-gray-700">Speakers</h5>
                                    <div class="space-y-4 mt-2">
                                        @foreach($personnel['speakers'] as $index => $speaker)
                                        <div class="p-3 bg-gray-50 rounded-md border shadow-sm" wire:key="speaker-{{ $index }}">
                                            <div class="flex space-x-4">
                                                
                                                {{-- Kolom Foto (Updated) --}}
                                                <div class="w-1/4 flex flex-col items-center gap-2">
                                                    {{-- Preview Image --}}
                                                    @if(!empty($speaker['photo']) && is_object($speaker['photo']))
                                                        <img src="{{ $speaker['photo']->temporaryUrl() }}" class="w-20 h-20 rounded-full object-cover border bg-white shadow-sm">
                                                    @elseif(!empty($speaker['photo_url']))
                                                        <div class="relative group">
                                                            <img src="{{ $speaker['photo_url'] }}" class="w-20 h-20 rounded-full object-cover border bg-white shadow-sm">
                                                            {{-- Badge Indikator Drive --}}
                                                            @if(str_contains($speaker['photo_url'], 'stream'))
                                                                <span class="absolute bottom-0 right-0 bg-blue-500 text-white text-[9px] px-1.5 py-0.5 rounded-full shadow border border-white">Drive</span>
                                                            @endif
                                                        </div>
                                                    @else
                                                        <div class="w-20 h-20 rounded-full bg-gray-200 flex items-center justify-center text-gray-400 border shadow-inner">
                                                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                                        </div>
                                                    @endif

                                                    {{-- Tombol Aksi Foto --}}
                                                    <div class="flex flex-col gap-1 w-full px-2">
                                                        {{-- Manual Upload --}}
                                                        <label class="cursor-pointer bg-white border border-gray-300 text-gray-600 text-[10px] py-1.5 px-2 rounded hover:bg-gray-100 text-center transition w-full shadow-sm">
                                                            Upload
                                                            <input type="file" wire:model="personnel.speakers.{{ $index }}.photo" class="hidden">
                                                        </label>
                                                        
                                                        {{-- Drive Picker --}}
                                                        {{-- Target dikirim sebagai: 'personnel.speakers.INDEX' --}}
                                                        <button type="button" 
                                                                wire:click="openFilePicker('personnel.speakers.{{ $index }}')" 
                                                                class="bg-indigo-50 border border-indigo-200 text-indigo-700 text-[10px] py-1.5 px-2 rounded hover:bg-indigo-100 transition w-full shadow-sm">
                                                            Select Drive
                                                        </button>
                                                    </div>
                                                </div>

                                                {{-- Nama & Instansi --}}
                                                <div class="flex-grow space-y-3">
                                                    <div>
                                                        <x-input-label value="Name" class="text-xs text-gray-500 mb-1"/>
                                                        <input type="text" wire:model="personnel.speakers.{{ $index }}.name" placeholder="Full Name" class="block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:border-indigo-500 focus:ring-indigo-500">
                                                    </div>
                                                    <div>
                                                        <x-input-label value="Organization / Job Title" class="text-xs text-gray-500 mb-1"/>
                                                        <input type="text" wire:model="personnel.speakers.{{ $index }}.organization" placeholder="Organization" class="block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:border-indigo-500 focus:ring-indigo-500">
                                                    </div>
                                                </div>

                                                {{-- Tombol Hapus Speaker --}}
                                                <button type="button" wire:click="removePersonnel('speakers', {{ $index }})" class="text-gray-400 hover:text-red-500 self-start p-1 transition-colors" title="Remove Speaker">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                </button>
                                            </div>

                                            {{-- Social Links --}}
                                            <div class="border-t border-gray-200 pt-3 mt-4">
                                                <div class="flex justify-between items-center mb-2">
                                                    <h5 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Social Links</h5>
                                                    <button type="button" wire:click="addSocialLink('speakers', {{ $index }})" class="text-xs font-medium text-indigo-600 hover:text-indigo-800 flex items-center">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                                        Add Link
                                                    </button>
                                                </div>

                                                @if (!empty($speaker['social_links']) && is_array($speaker['social_links']))
                                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                                        @foreach($speaker['social_links'] as $linkIndex => $link)
                                                        <div class="flex items-center space-x-2" wire:key="speaker-{{ $index }}-link-{{ $linkIndex }}">
                                                            {{-- Favicon --}}
                                                            <div class="w-6 h-6 flex items-center justify-center bg-white border rounded-full flex-shrink-0 overflow-hidden">
                                                                @if(!empty($link['favicon']))
                                                                    <img src="{{ $link['favicon'] }}" class="w-4 h-4">
                                                                @else
                                                                    <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                                                @endif
                                                            </div>

                                                            <input type="url"
                                                                wire:model.live.debounce.500ms="personnel.speakers.{{ $index }}.social_links.{{ $linkIndex }}.url"
                                                                placeholder="https://linkedin.com/in/..."
                                                                class="block w-full shadow-sm text-xs border-gray-300 rounded-md focus:border-indigo-500 focus:ring-indigo-500 py-1.5">

                                                            <button type="button" wire:click="removeSocialLink('speakers', {{ $index }}, {{ $linkIndex }})" class="text-gray-400 hover:text-red-500 p-1">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                            </button>
                                                        </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                    <button type="button" wire:click="addPersonnel('speakers')" class="mt-3 text-sm text-indigo-600 font-medium hover:text-indigo-800 flex items-center px-2 py-1 rounded hover:bg-indigo-50 w-fit">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                        Add Speaker
                                    </button>
                                </div>

                                {{-- Moderator Section --}}
                                <div class="mt-4 p-4 border rounded-md bg-white">
                                    <h5 class="font-semibold text-gray-700">Moderators</h5>
                                    <div class="space-y-4 mt-2">
                                        @foreach($personnel['moderators'] as $index => $moderator)
                                        <div class="p-3 bg-gray-50 rounded-md border shadow-sm" wire:key="moderator-{{ $index }}">
                                            <div class="flex space-x-4">
                                                
                                                {{-- Foto (Moderator) --}}
                                                <div class="w-1/4 flex flex-col items-center gap-2">
                                                    @if(!empty($moderator['photo']) && is_object($moderator['photo']))
                                                        <img src="{{ $moderator['photo']->temporaryUrl() }}" class="w-20 h-20 rounded-full object-cover border bg-white shadow-sm">
                                                    @elseif(!empty($moderator['photo_url']))
                                                        <div class="relative group">
                                                            <img src="{{ $moderator['photo_url'] }}" class="w-20 h-20 rounded-full object-cover border bg-white shadow-sm">
                                                            @if(str_contains($moderator['photo_url'], 'stream'))
                                                                <span class="absolute bottom-0 right-0 bg-blue-500 text-white text-[9px] px-1.5 py-0.5 rounded-full shadow border border-white">Drive</span>
                                                            @endif
                                                        </div>
                                                    @else
                                                        <div class="w-20 h-20 rounded-full bg-gray-200 flex items-center justify-center text-gray-400 border shadow-inner">
                                                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                                        </div>
                                                    @endif

                                                    <div class="flex flex-col gap-1 w-full px-2">
                                                        <label class="cursor-pointer bg-white border border-gray-300 text-gray-600 text-[10px] py-1.5 px-2 rounded hover:bg-gray-100 text-center transition w-full shadow-sm">
                                                            Upload
                                                            <input type="file" wire:model="personnel.moderators.{{ $index }}.photo" class="hidden">
                                                        </label>
                                                        
                                                        <button type="button" 
                                                                wire:click="openFilePicker('personnel.moderators.{{ $index }}')" 
                                                                class="bg-indigo-50 border border-indigo-200 text-indigo-700 text-[10px] py-1.5 px-2 rounded hover:bg-indigo-100 transition w-full shadow-sm">
                                                            Select Drive
                                                        </button>
                                                    </div>
                                                </div>

                                                {{-- Nama & Instansi --}}
                                                <div class="flex-grow space-y-3">
                                                    <div>
                                                        <x-input-label value="Name" class="text-xs text-gray-500 mb-1"/>
                                                        <input type="text" wire:model="personnel.moderators.{{ $index }}.name" placeholder="Full Name" class="block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:border-indigo-500 focus:ring-indigo-500">
                                                    </div>
                                                    <div>
                                                        <x-input-label value="Organization / Job Title" class="text-xs text-gray-500 mb-1"/>
                                                        <input type="text" wire:model="personnel.moderators.{{ $index }}.organization" placeholder="Organization" class="block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:border-indigo-500 focus:ring-indigo-500">
                                                    </div>
                                                </div>

                                                <button type="button" wire:click="removePersonnel('moderators', {{ $index }})" class="text-gray-400 hover:text-red-500 self-start p-1 transition-colors">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                </button>
                                            </div>

                                            {{-- Social Links --}}
                                            <div class="border-t border-gray-200 pt-3 mt-4">
                                                <div class="flex justify-between items-center mb-2">
                                                    <h5 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Social Links</h5>
                                                    <button type="button" wire:click="addSocialLink('moderators', {{ $index }})" class="text-xs font-medium text-indigo-600 hover:text-indigo-800 flex items-center">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                                        Add Link
                                                    </button>
                                                </div>

                                                @if (!empty($moderator['social_links']) && is_array($moderator['social_links']))
                                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                                        @foreach($moderator['social_links'] as $linkIndex => $link)
                                                        <div class="flex items-center space-x-2" wire:key="moderator-{{ $index }}-link-{{ $linkIndex }}">
                                                            <div class="w-6 h-6 flex items-center justify-center bg-white border rounded-full flex-shrink-0 overflow-hidden">
                                                                @if(!empty($link['favicon']))
                                                                    <img src="{{ $link['favicon'] }}" class="w-4 h-4">
                                                                @else
                                                                    <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                                                @endif
                                                            </div>
                                                            <input type="url"
                                                                wire:model.live.debounce.500ms="personnel.moderators.{{ $index }}.social_links.{{ $linkIndex }}.url"
                                                                placeholder="https://linkedin.com/in/..."
                                                                class="block w-full shadow-sm text-xs border-gray-300 rounded-md focus:border-indigo-500 focus:ring-indigo-500 py-1.5">
                                                            <button type="button" wire:click="removeSocialLink('moderators', {{ $index }}, {{ $linkIndex }})" class="text-gray-400 hover:text-red-500 p-1">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                            </button>
                                                        </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                    <button type="button" wire:click="addPersonnel('moderators')" class="mt-3 text-sm text-indigo-600 font-medium hover:text-indigo-800 flex items-center px-2 py-1 rounded hover:bg-indigo-50 w-fit">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                        Add Moderator
                                    </button>
                                </div>
                            </div>

                            {{-- Baris 3: Jadwal dan Agenda Harian Dinamis --}}
                            <div class="mt-6 border-t pt-4">
                                <h4 class="text-md font-medium text-gray-900">Agenda & Rundown Acara</h4>
                                <p class="text-sm text-gray-500 mb-4">Atur jadwal untuk setiap hari pelaksanaan event. Anda bisa menambahkan beberapa sesi agenda di setiap harinya.</p>

                                <div class="space-y-6">
                                    {{-- Perulangan untuk setiap HARI --}}
                                    @foreach($daily_schedules as $dayIndex => $schedule)
                                    <div class="p-4 border rounded-lg bg-white" wire:key="schedule-{{ $dayIndex }}">
                                        <div class="flex justify-between items-center mb-4">
                                            <div class="flex-grow">
                                                <label class="text-sm font-medium text-gray-700">Tanggal untuk Hari ke-{{ $dayIndex + 1 }}</label>
                                                <input type="date" wire:model="daily_schedules.{{ $dayIndex }}.date" class="mt-1 w-full md:w-1/3 shadow-sm sm:text-sm border-gray-300 rounded-md">
                                            </div>
                                            <button type="button" wire:click="removeSchedule({{ $dayIndex }})" class="text-red-500 hover:text-red-700 font-semibold">Hapus Hari</button>
                                        </div>

                                        {{-- Area untuk Agenda di dalam Hari --}}
                                        <div class="space-y-4 pl-4 border-l-2 border-blue-200">

                                            {{-- MULAI PERULANGAN UNTUK SETIAP SESI AGENDA DI HARI INI --}}
                                            @foreach($schedule['agenda'] as $agendaIndex => $agendaItem)
                                            <div class="p-3 bg-gray-50 rounded-md border" wire:key="agenda-{{ $dayIndex }}-{{ $agendaIndex }}">
                                                <div class="flex justify-end">
                                                    <button type="button" wire:click="removeAgenda({{ $dayIndex }}, {{ $agendaIndex }})" class="text-red-500 text-xl font-bold">&times;</button>
                                                </div>

                                                {{-- Waktu dan Judul Sesi --}}
                                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                                    <div>
                                                        <label class="block text-xs font-medium text-gray-600">Jam Mulai</label>
                                                        <input type="time" wire:model="daily_schedules.{{ $dayIndex }}.agenda.{{ $agendaIndex }}.start_time" class="mt-1 w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs font-medium text-gray-600">Jam Selesai</label>
                                                        <input type="time" wire:model="daily_schedules.{{ $dayIndex }}.agenda.{{ $agendaIndex }}.end_time" class="mt-1 w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                                    </div>
                                                    <div class="md:col-span-2">
                                                        <label class="block text-xs font-medium text-gray-600">Judul Sesi (EN)</label>
                                                        <input type="text" wire:model="daily_schedules.{{ $dayIndex }}.agenda.{{ $agendaIndex }}.title.en" placeholder="Session Title" class="mt-1 w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                                    </div>
                                                    <div class="md:col-span-2 md:col-start-3">
                                                        <label class="block text-xs font-medium text-gray-600">Judul Sesi (ID)</label>
                                                        <input type="text" wire:model="daily_schedules.{{ $dayIndex }}.agenda.{{ $agendaIndex }}.title.id" placeholder="Judul Sesi" class="mt-1 w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                                    </div>
                                                </div>

                                                {{-- Narasumber & Moderator --}}
                                                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                                                    <div>
                                                        <label class="block text-xs font-medium text-gray-600">Narasumber</label>
                                                        <select multiple wire:model="daily_schedules.{{ $dayIndex }}.agenda.{{ $agendaIndex }}.speaker_ids" class="mt-1 w-full shadow-sm sm:text-sm border-gray-300 rounded-md h-24">
                                                            @foreach($personnel['speakers'] as $speaker)
                                                            <option value="{{ $speaker['id'] }}">{{ $speaker['name'] }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs font-medium text-gray-600">Moderator</label>
                                                        <select multiple wire:model="daily_schedules.{{ $dayIndex }}.agenda.{{ $agendaIndex }}.moderator_ids" class="mt-1 w-full shadow-sm sm:text-sm border-gray-300 rounded-md h-24">
                                                            @foreach($personnel['moderators'] as $moderator)
                                                            <option value="{{ $moderator['id'] }}">{{ $moderator['name'] }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                {{-- Link Materi (Opsional) --}}
                                                <div class="mt-4">
                                                    <label class="block text-xs font-medium text-gray-600">Link Materi (Opsional)</label>
                                                    <input type="url" wire:model="daily_schedules.{{ $dayIndex }}.agenda.{{ $agendaIndex }}.materials_link" placeholder="https://..." class="mt-1 w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                                </div>

                                                {{-- Info Tambahan Dinamis --}}
                                                <div class="mt-4 border-t pt-3">
                                                    <label class="block text-xs font-medium text-gray-600 mb-2">Informasi Tambahan</label>
                                                    <div class="space-y-2">
                                                        @foreach($agendaItem['extra_info'] as $infoIndex => $info)
                                                        <div class="flex items-center space-x-2" wire:key="info-{{ $dayIndex }}-{{ $agendaIndex }}-{{ $infoIndex }}">
                                                            <input type="text" wire:model="daily_schedules.{{ $dayIndex }}.agenda.{{ $agendaIndex }}.extra_info.{{ $infoIndex }}.key" placeholder="Contoh: Tema Sesi" class="w-1/3 shadow-sm sm:text-sm border-gray-300 rounded-md">
                                                            <input type="text" wire:model="daily_schedules.{{ $dayIndex }}.agenda.{{ $agendaIndex }}.extra_info.{{ $infoIndex }}.value" placeholder="Isi informasinya" class="flex-grow shadow-sm sm:text-sm border-gray-300 rounded-md">
                                                            <button type="button" wire:click="removeExtraInfo({{ $dayIndex }}, {{ $agendaIndex }}, {{ $infoIndex }})" class="text-red-500 font-bold">X</button>
                                                        </div>
                                                        @endforeach
                                                    </div>
                                                    <button type="button" wire:click="addExtraInfo({{ $dayIndex }}, {{ $agendaIndex }})" class="mt-2 text-xs text-green-600 font-semibold">+ Tambah Informasi</button>
                                                </div>
                                            </div>
                                            @endforeach
                                            {{-- SELESAI PERULANGAN UNTUK AGENDA --}}

                                            {{-- Tombol untuk menambah AGENDA BARU di hari ini --}}
                                            <button type="button" wire:click="addAgenda({{ $dayIndex }})" class="mt-4 text-sm text-blue-600 font-semibold">+ Tambah Sesi Agenda</button>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>

                                {{-- Tombol untuk menambah HARI BARU --}}
                                <button type="button" wire:click="addSchedule" class="mt-4 bg-blue-100 text-blue-700 font-bold py-2 px-4 rounded w-full text-center">
                                    + Tambah Hari Pelaksanaan
                                </button>
                            </div>

                            <div x-show="!$wire.use_external_link" x-transition>

                                {{-- TIPE EVENT --}}
                                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block font-medium text-sm text-gray-700">Event Type</label>
                                        <div class="flex items-center space-x-4 mt-1">
                                            <label class="flex items-center" wire:key="type-offline">
                                                <input type="radio" wire:model.live="type" value="offline" class="form-radio ...">
                                                <span class="ml-2 text-sm text-gray-600">Offline</span>
                                            </label>
                                            <label class="flex items-center" wire:key="type-online">
                                                <input type="radio" wire:model.live="type" value="online" class="form-radio ...">
                                                <span class="ml-2 text-sm text-gray-600">Online</span>
                                            </label>
                                            <label class="flex items-center" wire:key="type-hybrid">
                                                <input type="radio" wire:model.live="type" value="hybrid" class="form-radio ...">
                                                <span class="ml-2 text-sm text-gray-600">Hybrid</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                {{-- BAGIAN OFFLINE (Muncul jika tipe 'offline' atau 'hybrid') --}}
                                @if ($type === 'offline' || $type === 'hybrid')
                                <div class="mt-4 space-y-4 p-4 border rounded-md {{ $type === 'hybrid' ? 'bg-blue-50' : '' }}">
                                    <h4 class="font-semibold text-gray-800">Detail Lokasi Offline</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label for="venue_en" class="block font-medium text-sm text-gray-700">Venue (English)</label>
                                            <input id="venue_en" type="text" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" wire:model.defer="venue_en" />
                                            @error('venue_en') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <label for="venue_id" class="block font-medium text-sm text-gray-700">Venue (Indonesia)</label>
                                            <input id="venue_id" type="text" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" wire:model.defer="venue_id" />
                                            @error('venue_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                    <div>
                                        <label for="google_maps_iframe" class="block text-sm font-medium text-gray-700">
                                            Google Maps Iframe Embed
                                        </label>

                                        <textarea wire:model.defer="google_maps_iframe" id="google_maps_iframe" rows="4"
                                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                            placeholder="Tempel kode iframe dari Google Maps di sini. Contoh: <iframe src=..."></textarea>

                                        <p class="mt-1 text-xs text-gray-500">
                                            <b>Cara mendapatkan:</b> Buka Google Maps > Cari lokasi > Share > Embed a map > COPY HTML.
                                        </p>

                                        @error('google_maps_iframe')
                                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                @endif

                                {{-- JIKA TIPE ONLINE --}}
                                @if ($type === 'online' || $type === 'hybrid')
                                <div class="mt-4 space-y-4 p-4 border rounded-md bg-gray-50">
                                    <div>
                                        <label for="meeting_link" class="block font-medium text-sm text-gray-700">Meeting Link (e.g., Zoom, GMeet)</label>
                                        <input id="meeting_link" type="text" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" wire:model.defer="meeting_link" placeholder="https://zoom.us/j/..." />
                                    </div>

                                    <div>
                                        <label for="platform" class="block font-medium text-sm text-gray-700">Platform</label>
                                        <select id="platform" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" wire:model.live="platform">
                                            <option value="">-- Select Platform --</option>
                                            <option value="Zoom Meeting">Zoom Meeting</option>
                                            <option value="Google Meet">Google Meet</option>
                                            <option value="Microsoft Teams">Microsoft Teams</option>
                                            <option value="Lainnya...">Lainnya...</option>
                                        </select>
                                    </div>

                                    {{-- Info Tambahan Sesuai Platform --}}
                                    @if ($platform === 'Zoom Meeting')
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label for="meeting_id" class="block font-medium text-sm text-gray-700">Meeting ID</label>
                                            <input id="meeting_id" type="text" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" wire:model.defer="meeting_info.meeting_id" />
                                        </div>
                                        <div>
                                            <label for="passcode" class="block font-medium text-sm text-gray-700">Passcode</label>
                                            <input id="passcode" type="text" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" wire:model.defer="meeting_info.passcode" />
                                        </div>
                                    </div>
                                    @elseif ($platform === 'Lainnya...')
                                    <div class="space-y-4">
                                        <div>
                                            <label for="platform_name" class="block font-medium text-sm text-gray-700">Platform Name</label>
                                            <input id="platform_name" type="text" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" wire:model.defer="meeting_info.platform_name" placeholder="e.g., Discord, YouTube Live" />
                                        </div>
                                        <div>
                                            <label for="instructions" class="block font-medium text-sm text-gray-700">Joining Instructions</label>
                                            <textarea id="instructions" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" wire:model.defer="meeting_info.instructions"></textarea>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                                @endif

                            </div>

                            <div class="mt-4 p-4 border border-gray-200 rounded-md">
                                <label class="block text-sm font-medium text-gray-700">Link Rekaman YouTube</label>
                                <p class="text-xs text-gray-500 mt-1 mb-4">Isi jika event sudah selesai dan memiliki video rekaman. Bisa tambah lebih dari satu.</p>

                                <div class="space-y-4">
                                    @foreach ($youtube_recordings as $index => $recording)
                                    <div class="flex items-center space-x-2 p-3 bg-gray-50 rounded" wire:key="recording-{{ $index }}">
                                        <div class="flex-grow">
                                            <input type="text" wire:model="youtube_recordings.{{ $index }}.title" placeholder="Judul Video (cth: Day 1 - Opening)" class="block w-full text-sm border-gray-300 rounded-md shadow-sm">
                                            <input type="url" wire:model="youtube_recordings.{{ $index }}.link" placeholder="https://www.youtube.com/watch?v=xxxx" class="block w-full text-sm mt-1 border-gray-300 rounded-md shadow-sm">
                                        </div>
                                        <button type="button" wire:click="removeYoutubeRecording({{ $index }})" class="text-red-500 hover:text-red-700 p-1">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                    @endforeach
                                </div>

                                <button type="button" wire:click.prevent="addYoutubeRecording" class="mt-4 text-sm font-semibold text-indigo-600 hover:text-indigo-800">+ Tambah Link Rekaman</button>
                            </div>

                            <div x-show="!$wire.use_external_link" x-transition>

                                {{-- Baris 6: Form Registrasi --}}
                                <div class="mt-4">
                                    <label class="block text-sm font-medium text-gray-700">Registration Form</label>
                                    <select wire:model.defer="inquiry_form_id" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                        <option value="">-- No Custom Form --</option>
                                        @foreach($allForms as $form)<option value="{{ $form->id }}">{{ $form->name }}</option>@endforeach
                                    </select>
                                </div>
                            </div>

                            <div x-show="!$wire.use_external_link" x-transition>
                                {{-- Template Email Konfirmasi --}}
                                <div class="mt-4">
                                    <label class="block text-sm font-medium text-gray-700">Select a confirmation email template</label>
                                    <select wire:model.defer="confirmation_template_id" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                        <option value="">-- Do Not Send Confirmation Email --</option>
                                        @foreach($availableTemplates as $template)
                                        <option value="{{ $template->id }}">{{ $template->subject }}</option>
                                        @endforeach
                                    </select>
                                    <p class="mt-1 text-xs text-gray-500">Pilih email template eticket yang akan dikirim otomatis setelah pendaftaran berhasil. Kosongkan untuk tidak mengirim email.</p>
                                </div>
                            </div>

                            {{-- Baris 7: Deskripsi --}}
                            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div wire:ignore><label class="block text-sm font-medium text-gray-700">Description (EN)</label><x-ckeditor wire:model.defer="description_en" id="description_en"></x-ckeditor></div>
                                <div wire:ignore><label class="block text-sm font-medium text-gray-700">Description (ID)</label><x-ckeditor wire:model.defer="description_id" id="description_id"></x-ckeditor></div>
                            </div>

                            {{-- Baris 8: Banner --}}
                            <div class="mt-4">
                                <x-input-label for="banner" value="{{ __('Event Banner') }}" />
                                
                                <div class="flex items-center gap-2 mt-1">
                                    <input type="file" wire:model="banner" id="banner" class="hidden" x-ref="bannerInput">
                                    
                                    <x-secondary-button @click="$refs.bannerInput.click()" type="button">
                                        {{ __('Upload dari Komputer') }}
                                    </x-secondary-button>
                            
                                    <span class="text-gray-400">atau</span>
                            
                                    <x-secondary-button wire:click="openFilePicker('banner')" type="button" class="bg-indigo-50 text-indigo-700 border-indigo-200">
                                        {{ __('Pilih dari Google Drive') }}
                                    </x-secondary-button>
                                </div>
                            
                                <input type="hidden" id="drive_banner_path" name="drive_banner_path">
                            
                                <div class="mt-2">
                                    @if ($banner)
                                        <img src="{{ $banner->temporaryUrl() }}" class="h-40 w-auto rounded-lg shadow object-cover">
                                    @elseif ($existingBannerUrl)
                                        <div class="relative inline-block group">
                                            <img src="{{ $existingBannerUrl }}" class="h-40 w-auto rounded-lg shadow object-cover">
                                            <div class="absolute inset-0 bg-black/50 hidden group-hover:flex items-center justify-center rounded-lg text-white text-xs">
                                                {{ str_contains($existingBannerUrl, 'stream') ? 'Google Drive' : 'Local Storage' }}
                                            </div>
                                        </div>
                                    @endif
                                    <x-input-error for="banner" class="mt-2" />
                                </div>
                            </div>

                            {{-- ====================================================== --}}
                            {{-- BAGIAN BARU: KONFIGURASI FIELD TAMBAHAN --}}
                            {{-- ====================================================== --}}
                            <div x-show="!$wire.use_external_link" x-transition>
                                <div class="mt-6 border-t pt-4">
                                    <h4 class="text-md font-medium text-gray-900">Konfigurasi Field Tambahan</h4>
                                    <p class="text-sm text-gray-500 mb-4">Aktifkan field pendaftaran tambahan yang dibutuhkan untuk event ini.</p>

                                    <div class="space-y-4">
                                        {{-- Nama Instansi --}}
                                        <div class="flex items-start p-3 bg-gray-50 rounded-md border">
                                            <div class="flex items-center h-5"><input type="checkbox" wire:model.live="fieldConfig.nama_instansi.active" class="h-4 w-4 text-blue-600 border-gray-300 rounded"></div>
                                            <div class="ml-3 text-sm flex-grow">
                                                <label class="font-medium text-gray-700">Nama Instansi</label>
                                                @if($fieldConfig['nama_instansi']['active'])
                                                <div class="mt-2 flex items-center">
                                                    <input type="checkbox" wire:model="fieldConfig.nama_instansi.required" class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                                                    <label class="ml-2 text-gray-600">Wajib diisi</label>
                                                </div>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- Phone Number --}}
                                        <div class="flex items-start p-3 bg-gray-50 rounded-md border">
                                            <div class="flex items-center h-5"><input type="checkbox" wire:model.live="fieldConfig.phone_number.active" class="h-4 w-4 text-blue-600 border-gray-300 rounded"></div>
                                            <div class="ml-3 text-sm flex-grow">
                                                <label class="font-medium text-gray-700">Phone Number</label>
                                                @if($fieldConfig['phone_number']['active'])
                                                <div class="mt-2 flex items-center">
                                                    <input type="checkbox" wire:model="fieldConfig.phone_number.required" class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                                                    <label class="ml-2 text-gray-600">Wajib diisi</label>
                                                </div>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- Tipe Instansi --}}
                                        <div class="flex items-start p-3 bg-gray-50 rounded-md border">
                                            <div class="flex items-center h-5"><input type="checkbox" wire:model.live="fieldConfig.tipe_instansi.active" class="h-4 w-4 text-blue-600 border-gray-300 rounded"></div>
                                            <div class="ml-3 text-sm flex-grow">
                                                <label class="font-medium text-gray-700">Tipe Instansi</label>
                                                @if($fieldConfig['tipe_instansi']['active'])
                                                <div class="mt-2">
                                                    <div class="flex items-center mb-2">
                                                        <input type="checkbox" wire:model="fieldConfig.tipe_instansi.required" class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                                                        <label class="ml-2 text-gray-600">Wajib diisi</label>
                                                    </div>
                                                    <label class="text-xs text-gray-500">Pilihan Dropdown (pisahkan dengan koma)</label>
                                                    <input type="text" wire:model.defer="fieldConfig.tipe_instansi.options" placeholder="Pemerintahan, Swasta, Akademisi, LSM" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                                    <p class="text-xs text-gray-500 mt-1">Pilihan "Others" akan ditambahkan secara otomatis.</p>
                                                </div>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- Jabatan --}}
                                        <div class="flex items-start p-3 bg-gray-50 rounded-md border">
                                            <div class="flex items-center h-5"><input type="checkbox" wire:model.live="fieldConfig.jabatan.active" class="h-4 w-4 text-blue-600 border-gray-300 rounded"></div>
                                            <div class="ml-3 text-sm flex-grow">
                                                <label class="font-medium text-gray-700">Jabatan</label>
                                                @if($fieldConfig['jabatan']['active'])
                                                <div class="mt-2 flex items-center">
                                                    <input type="checkbox" wire:model="fieldConfig.jabatan.required" class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                                                    <label class="ml-2 text-gray-600">Wajib diisi</label>
                                                </div>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- Alamat --}}
                                        <div class="flex items-start p-3 bg-gray-50 rounded-md border">
                                            <div class="flex items-center h-5"><input type="checkbox" wire:model.live="fieldConfig.alamat.active" class="h-4 w-4 text-blue-600 border-gray-300 rounded"></div>
                                            <div class="ml-3 text-sm flex-grow">
                                                <label class="font-medium text-gray-700">Alamat</label>
                                                @if($fieldConfig['alamat']['active'])
                                                <div class="mt-2 flex items-center">
                                                    <input type="checkbox" wire:model="fieldConfig.alamat.required" class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                                                    <label class="ml-2 text-gray-600">Wajib diisi</label>
                                                </div>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- Tanda Tangan --}}
                                        <div class="flex items-start p-3 bg-gray-50 rounded-md border">
                                            <div class="flex items-center h-5"><input type="checkbox" wire:model.live="fieldConfig.tanda_tangan.active" class="h-4 w-4 text-blue-600 border-gray-300 rounded"></div>
                                            <div class="ml-3 text-sm flex-grow">
                                                <label class="font-medium text-gray-700">Tanda Tangan</label>
                                                @if($fieldConfig['tanda_tangan']['active'])
                                                <div class="mt-2 flex items-center">
                                                    <input type="checkbox" wire:model="fieldConfig.tanda_tangan.required" class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                                                    <label class="ml-2 text-gray-600">Wajib diisi</label>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Baris 10: Sponsors --}}
                            {{-- BAGIAN SPONSORS & PARTNERSHIP (GROUPING) --}}
                            <div class="mt-6 border-t pt-6">
                                <div class="flex justify-between items-center mb-4">
                                    <div>
                                        <h4 class="text-lg font-medium text-gray-900">Sponsors & Partners</h4>
                                        <p class="text-sm text-gray-500">Buat kategori (misal: "Gold Sponsor" atau "Media Partner") lalu masukkan daftar perusahaannya.</p>
                                    </div>
                                    <button type="button" wire:click="addSponsorCategory" class="text-sm px-4 py-2 bg-gray-800 text-white rounded hover:bg-gray-700 transition">
                                        + Tambah Kategori Baru
                                    </button>
                                </div>
                            
                                <div class="space-y-6">
                                    @foreach($sponsors as $catIndex => $category)
                                        <div class="p-6 mb-4 border-2 border-gray-200 rounded-xl bg-white relative" wire:key="cat-{{ $catIndex }}">
                                            
                                            {{-- Header Kategori --}}
                                            <div class="flex gap-4 items-start mb-6 pb-4 border-b border-gray-100">
                                                <div class="flex-1">
                                                    <x-input-label>Nama Kategori / Judul Group</x-input-label>
                                                    <x-text-input 
                                                        wire:model="sponsors.{{ $catIndex }}.category_name" 
                                                        class="w-full text-lg font-bold text-blue-700" 
                                                        placeholder="Contoh: Platinum Sponsor / Official Media Partner" 
                                                    />
                                                </div>
                                                <button type="button" wire:click="removeSponsorCategory({{ $catIndex }})" class="mt-6 text-red-500 hover:text-red-700 text-sm font-medium">
                                                    Hapus Kategori
                                                </button>
                                            </div>
                            
                                            {{-- List Item di dalam Kategori --}}
                                            <div class="grid grid-cols-1 gap-4 pl-4 border-l-4 border-gray-100">
                                                @if(empty($category['items']))
                                                    <div class="text-sm text-gray-400 italic">Belum ada perusahaan di kategori ini.</div>
                                                @else
                                                    @foreach($category['items'] as $itemIndex => $item)
                                                        <div class="flex flex-col md:flex-row gap-4 items-start p-3 bg-gray-50 rounded-lg border border-gray-200" wire:key="item-{{ $catIndex }}-{{ $itemIndex }}">
                                                            
                                                            {{-- Input Logo (Hybrid) --}}
                                                            <div class="w-32 shrink-0 flex flex-col gap-2">
                                                                {{-- Area Preview --}}
                                                                <div class="h-24 w-full bg-white border border-gray-300 rounded flex items-center justify-center overflow-hidden relative group">
                                                                    @if(!empty($item['logo']) && is_object($item['logo']))
                                                                        {{-- Preview Upload Manual --}}
                                                                        <img src="{{ $item['logo']->temporaryUrl() }}" class="object-contain h-full w-full">
                                                                    @elseif(!empty($item['logo_url']))
                                                                        {{-- Preview Existing / Drive --}}
                                                                        <img src="{{ $item['logo_url'] }}" class="object-contain h-full w-full">
                                                                        
                                                                        {{-- Badge Indikator Drive --}}
                                                                        @if(str_contains($item['logo_url'], 'stream'))
                                                                             <span class="absolute bottom-0 right-0 bg-green-500 text-white text-[9px] px-1.5 py-0.5 rounded-tl shadow">Drive</span>
                                                                        @endif
                                                                    @else
                                                                        <div class="text-center p-2">
                                                                            <svg class="w-8 h-8 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                                            <span class="text-[10px] text-gray-400">No Logo</span>
                                                                        </div>
                                                                    @endif
                                                                </div>

                                                                {{-- Tombol Aksi --}}
                                                                <div class="flex flex-col gap-1">
                                                                    {{-- Tombol Upload Manual --}}
                                                                    <label class="cursor-pointer bg-white border border-gray-300 text-gray-600 text-[10px] py-1 px-2 rounded hover:bg-gray-50 text-center transition w-full shadow-sm">
                                                                        Upload File
                                                                        <input type="file" wire:model="sponsors.{{ $catIndex }}.items.{{ $itemIndex }}.logo" class="hidden">
                                                                    </label>
                                                                    
                                                                    {{-- Tombol Pilih Drive --}}
                                                                    <button type="button" 
                                                                            {{-- Kirim target spesifik: sponsors.0.items.1 --}}
                                                                            wire:click="openFilePicker('sponsors.{{ $catIndex }}.items.{{ $itemIndex }}')" 
                                                                            class="bg-green-50 border border-green-200 text-green-700 text-[10px] py-1 px-2 rounded hover:bg-green-100 transition w-full shadow-sm">
                                                                        Pilih dari Drive
                                                                    </button>
                                                                </div>
                                                            </div>
                            
                                                            {{-- Input Text --}}
                                                            <div class="flex-1 space-y-2 w-full">
                                                                <input type="text" 
                                                                    wire:model="sponsors.{{ $catIndex }}.items.{{ $itemIndex }}.name" 
                                                                    class="block w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                                                                    placeholder="Nama Perusahaan"
                                                                >
                                                                <input type="url" 
                                                                    wire:model="sponsors.{{ $catIndex }}.items.{{ $itemIndex }}.website" 
                                                                    class="block w-full text-xs text-gray-500 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                                                                    placeholder="https://website.com"
                                                                >
                                                            </div>
                            
                                                            {{-- Hapus Item --}}
                                                            <button type="button" wire:click="removeSponsorItem({{ $catIndex }}, {{ $itemIndex }})" class="text-gray-400 hover:text-red-500 pt-2">
                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                            </button>
                                                        </div>
                                                    @endforeach
                                                @endif
                            
                                                <button type="button" wire:click="addSponsorItem({{ $catIndex }})" class="mt-2 flex items-center justify-center w-full py-2 border-2 border-dashed border-gray-300 rounded-lg text-sm text-gray-500 hover:border-blue-400 hover:text-blue-500 hover:bg-blue-50 transition">
                                                    + Tambah Perusahaan ke "{{ $category['category_name'] ?: 'Kategori Ini' }}"
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            
                            
                            <div class="mt-6 border-t pt-4">
                                <label class="block text-sm font-medium text-gray-700">Visibilitas</label>
                                <select wire:model.defer="visibility" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    <option value="public">Publik (Tampil di list event)</option>
                                    <option value="private">Privat (Hanya via link)</option>
                                </select>
                            </div>
                            <div class="col-span-6 sm:col-span-3 mb-6">
                                {{-- Toggle Paid Event --}}
                                <div class="flex items-center h-full pt-6">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" wire:model="is_paid_event" class="sr-only peer">
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                        <span class="ml-3 text-sm font-medium text-gray-900">Paid Event (Berbayar)</span>
                                    </label>
                                </div>
                                <p class="text-xs text-gray-500 mt-1" x-show="$wire.is_paid_event">
                                    * Tiket akan dikelola terpisah setelah Event disimpan.
                                </p>
                            </div>

                            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div x-show="!$wire.use_external_link" x-transition>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Quota</label>
                                        <input type="number" wire:model.defer="quota" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Registration Status</label>
                                    <select wire:model.defer="is_active" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>



                        {{-- Tombol Simpan & Batal --}}
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">

                            <x-primary-button type="submit" class="sm:ml-3" wire:loading.attr="disabled" wire:target="store">
                                <span wire:loading wire:target="store">
                                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Saving...
                                </span>
                                <span wire:loading.remove wire:target="store">
                                    Save
                                </span>
                            </x-primary-button>

                            <x-secondary-button type="button" wire:click="closeModal()" class="mt-3 sm:mt-0" wire:loading.attr="disabled" wire:target="store">
                                Cancel
                            </x-secondary-button>

                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif
        
        <x-modal name="file-manager-picker" :show="$showFilePicker" maxWidth="7xl" focusable>
            <div class="p-4 h-[85vh] flex flex-col">
                <div class="flex justify-between items-center mb-4 border-b pb-2">
                    <h2 class="text-lg font-semibold text-gray-800">Pilih File dari Google Drive</h2>
                    <button wire:click="closeFilePicker" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                @if($showFilePicker)
                    @livewire('admin.file-manager.index', [
                        'isPicker' => true,
                        'eventNameToEmit' => 'fileSelected'
                    ], key('file-manager-picker-'.time()))
                @endif
            </div>
        </x-modal>

        @if($showTicketModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">

                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeTicketModal"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-5xl w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="flex justify-between items-center mb-6 border-b pb-2">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                Manage Tickets: <span class="font-bold text-blue-600">{{ $selectedEventForTicket->name_en }}</span>
                            </h3>
                            <button wire:click="closeTicketModal" class="text-gray-400 hover:text-gray-500">
                                <span class="sr-only">Close</span>
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        @if($selectedEventForTicket)
                        <livewire:admin.event.ticket-manager :event="$selectedEventForTicket" wire:key="ticket-manager-{{ $selectedEventForTicket->id }}" />
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    @push('scripts')
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('load-content-to-editors', (data) => {
                // Log 1: Memastikan event diterima
                console.log('Event "load-content-to-editors" DITERIMA.', data);

                setTimeout(function() {
                    let editor_en = tinymce.get('description_en');
                    let editor_id = tinymce.get('description_id');

                    // Log 2: Memeriksa apakah editor ditemukan
                    console.log('Mencari editor EN:', editor_en);
                    console.log('Mencari editor ID:', editor_id);

                    if (editor_en && editor_id) {
                        editor_en.setContent(data.description_en);
                        editor_id.setContent(data.description_id);
                        // Log 3: Konfirmasi bahwa konten sudah diatur
                        console.log('Konten BERHASIL diatur ke kedua editor.');
                    } else {
                        console.error('GAGAL: Satu atau kedua editor tidak ditemukan.');
                    }
                }, 200); // Timeout dinaikkan sedikit menjadi 200ms
            });
        });
    </script>
    @endpush