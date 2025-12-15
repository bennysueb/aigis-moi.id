<div>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Update Exhibitor Profile') }}
            </h2>
            <x-secondary-button onclick="startTour(true)">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Mulai Tur
            </x-secondary-button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm-px-6 lg-px-8 space-y-6">

            {{-- CARD 1: INFORMASI INSTANSI --}}
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <section>
                    <header>
                        <h2 class="text-lg font-medium text-gray-900" id="tour-step-1">{{ __('profile.information') }}</h2>
                        <p class="mt-1 text-sm text-gray-600">{{ __('profile.information_description') }}</p>
                    </header>
                    <form wire:submit.prevent="saveInstansi" class="mt-6 space-y-6">

                        <div>
                            <x-input-label for="logo" value="{{ __('profile.instruction_upload_logo') }}" />
                            <div class="mt-4 flex items-center space-x-4">

                                <div class="h-20 w-20 aspect-square rounded-full bg-gray-100 border flex items-center justify-center overflow-hidden relative group">
                                    @if ($instansiForm->logo)
                                    {{-- Preview untuk logo yang baru di-upload --}}
                                    <img src="{{ $instansiForm->logo->temporaryUrl() }}" class="max-h-full max-w-full object-contain">
                                    @elseif ($instansiForm->user->logo_path)
                                    {{-- Menampilkan logo yang sudah ada --}}
                                    <img src="{{ asset('storage/' . $instansiForm->user->logo_path) }}" class="max-h-full max-w-full object-contain">

                                    {{-- Tombol Hapus yang Muncul Saat Hover --}}
                                    <button type="button"
                                        @click="$wire.set('actionToConfirm', 'removeLogo'); $wire.set('showConfirmModal', true)"
                                        class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center text-white opacity-0 group-hover:opacity-100 transition-opacity"
                                        aria-label="{{ __('profile.delete_logo') }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    @else
                                    {{-- Placeholder jika tidak ada logo --}}
                                    <div class="flex items-center justify-center"><i class="fas fa-image text-2xl text-gray-400"></i></div>
                                    @endif
                                </div>
                                <div x-data="{ filename: '' }" class="w-full">
                                    <div class="mt-1 flex items-center justify-between border border-gray-300 rounded-md shadow-sm">
                                        <span x-text="filename || 'No files selected'" class="px-3 text-sm text-gray-600 truncate"></span>
                                        <label for="logo" class="cursor-pointer inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-r-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">{{ __('profile.choose_file') }}</label>
                                    </div>
                                    <input @change="filename = $event.target.files[0] ? $event.target.files[0].name : ''" wire:model="instansiForm.logo" id="logo" type="file" class="sr-only">
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('instansiForm.logo')" class="mt-2" />
                        </div>

                        <div><x-input-label for="nama_instansi" value="{{ __('profile.institution_name') }}" /><x-text-input wire:model="instansiForm.nama_instansi" id="nama_instansi" type="text" class="mt-1 block w-full" /><x-input-error :messages="$errors->get('instansiForm.nama_instansi')" class="mt-2" /></div>
                        <div>
                            <x-input-label for="booth_number" value="{{ __('profile.booth_number') }}" />
                            <x-text-input wire:model="instansiForm.booth_number" id="booth_number" type="text" class="mt-1 block w-full" placeholder="eg. A - 104" />
                            <x-input-error :messages="$errors->get('instansiForm.booth_number')" class="mt-2" />
                        </div>
                        <div><x-input-label for="tipe_instansi" value="{{ __('profile.type_instansi') }}" /><x-text-input wire:model="instansiForm.tipe_instansi" id="tipe_instansi" type="text" class="mt-1 block w-full" /><x-input-error :messages="$errors->get('instansiForm.tipe_instansi')" class="mt-2" /></div>
                        <div><x-input-label for="phone_instansi" value="{{ __('profile.phone_instansi') }}" /><x-text-input wire:model="instansiForm.phone_instansi" id="phone_instansi" type="text" class="mt-1 block w-full" /><x-input-error :messages="$errors->get('instansiForm.phone_instansi')" class="mt-2" /></div>
                        <div><x-input-label for="alamat" value="{{ __('profile.address') }}" /><textarea wire:model="instansiForm.alamat" id="alamat" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea><x-input-error :messages="$errors->get('instansiForm.alamat')" class="mt-2" /></div>
                        <div><x-input-label for="description" value="{{ __('profile.description') }}" /><textarea wire:model="instansiForm.description" id="description" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea><x-input-error :messages="$errors->get('instansiForm.description')" class="mt-2" /></div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('profile.save') }}</x-primary-button>
                            <x-action-message class="me-3" on="saved-instansi">{{ __('profile.saved') }}</x-action-message>
                        </div>
                    </form>
                </section>
            </div>

            {{-- CARD 2: INFORMASI KONTAK PERSONAL --}}
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <section>
                    <header>
                        <h2 class="text-lg font-medium text-gray-900" id="tour-step-2">{{ __('profile.contact_information') }}</h2>
                        <p class="mt-1 text-sm text-gray-600">{{ __('profile.contact_information_description') }}</p>
                    </header>
                    <form wire:submit.prevent="saveContact" class="mt-6 space-y-6">
                        <div><x-input-label for="name" value="{{ __('profile.name') }}" /><x-text-input wire:model="contactForm.name" id="name" type="text" class="mt-1 block w-full" /><x-input-error :messages="$errors->get('contactForm.name')" class="mt-2" /></div>
                        <div><x-input-label for="email" value="{{ __('profile.email') }}" /><x-text-input wire:model="contactForm.email" id="email" type="email" class="mt-1 block w-full" /><x-input-error :messages="$errors->get('contactForm.email')" class="mt-2" /></div>
                        <div><x-input-label for="jabatan" value="{{ __('profile.position') }}" /><x-text-input wire:model="contactForm.jabatan" id="jabatan" type="text" class="mt-1 block w-full" /><x-input-error :messages="$errors->get('contactForm.jabatan')" class="mt-2" /></div>
                        <div><x-input-label for="whatsapp" value="{{ __('profile.whatsapp') }}" /><x-text-input wire:model="contactForm.whatsapp" id="whatsapp" type="text" class="mt-1 block w-full" placeholder="{{ __('profile.whatsapp_placeholder') }}" /><x-input-error :messages="$errors->get('contactForm.whatsapp')" class="mt-2" /></div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('profile.save') }}</x-primary-button>
                            <x-action-message class="me-3" on="saved-kontak">{{ __('profile.saved') }}</x-action-message>
                        </div>
                    </form>
                </section>
            </div>

            {{-- CARD 3: MEDIA SOSIAL & WEBSITE --}}
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <section>
                    <header>
                        <h2 class="text-lg font-medium text-gray-900" id="tour-step-3">{{ __('profile.social_media_and_website') }}</h2>
                        <p class="mt-1 text-sm text-gray-600">{{ __('profile.social_media_and_website_description') }}</p>
                    </header>
                    <form wire:submit.prevent="saveSocial" class="mt-6 space-y-6">
                        <div><x-input-label for="website" value="{{ __('profile.website') }}" /><x-text-input wire:model="socialForm.website" id="website" type="url" class="mt-1 block w-full" placeholder="{{ __('profile.website_placeholder') }}" /><x-input-error :messages="$errors->get('socialForm.website')" class="mt-2" /></div>
                        <div><x-input-label for="linkedin" value="{{ __('profile.linkedin') }}" /><x-text-input wire:model="socialForm.linkedin" id="linkedin" type="url" class="mt-1 block w-full" placeholder="{{ __('profile.linkedin_placeholder') }}" /><x-input-error :messages="$errors->get('socialForm.linkedin')" class="mt-2" /></div>
                        <div><x-input-label for="instagram" value="{{ __('profile.instagram') }}" /><x-text-input wire:model="socialForm.instagram" id="instagram" type="url" class="mt-1 block w-full" placeholder="{{ __('profile.instagram_placeholder') }}" /><x-input-error :messages="$errors->get('socialForm.instagram')" class="mt-2" /></div>
                        <div><x-input-label for="facebook" value="{{ __('profile.facebook') }}" /><x-text-input wire:model="socialForm.facebook" id="facebook" type="url" class="mt-1 block w-full" placeholder="{{ __('profile.facebook_placeholder') }}" /><x-input-error :messages="$errors->get('socialForm.facebook')" class="mt-2" /></div>
                        <div><x-input-label for="youtube_link" value="{{ __('profile.youtube_link') }}" /><x-text-input wire:model="socialForm.youtube_link" id="youtube_link" type="url" class="mt-1 block w-full" placeholder="{{ __('profile.youtube_link_placeholder') }}" /><x-input-error :messages="$errors->get('socialForm.youtube_link')" class="mt-2" /></div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('profile.save') }}</x-primary-button>
                            <x-action-message class="me-3" on="saved-medsos">{{ __('profile.saved') }}</x-action-message>
                        </div>
                    </form>
                </section>
            </div>

            {{-- CARD 4: MATERI DIGITAL --}}
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <section>
                    <header>
                        <h2 class="text-lg font-medium text-gray-900" id="tour-step-4">{{ __('profile.digital_material') }}</h2>
                        <p class="mt-1 text-sm text-gray-600">{{ __('profile.digital_material_description') }}</p>
                    </header>
                    <form wire:submit.prevent="saveMaterial" class="mt-6 space-y-6">
                        <div x-data="{ filename: '' }">
                            <x-input-label for="document" value="{{ __('profile.instruction.upload.dokumen')}}" />
                            <div class="mt-1 flex items-center justify-between border border-gray-300 rounded-md shadow-sm">
                                <span x-text="filename || 'No files selected'" class="px-3 text-sm text-gray-600 truncate"></span>
                                <label for="document" class="cursor-pointer inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-r-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">{{ __('profile.choose_file') }}</label>
                            </div>
                            <input @change="filename = $event.target.files[0] ? $event.target.files[0].name : ''" wire:model="materialForm.document" id="document" type="file" class="sr-only">

                            {{-- Bagian yang Diperbarui --}}
                            @if($materialForm->user?->document_path)
                            <div class="text-sm mt-2 text-gray-600 flex items-center justify-between">
                                <span>
                                    File saat ini: <a href="{{ asset('storage/' . $materialForm->user->document_path) }}" target="_blank" class="text-indigo-600 hover:underline">{{ basename($materialForm->user->document_path) }}</a>
                                </span>

                                <button type="button"
                                    @click="$wire.set('actionToConfirm', 'removeDocument'); $wire.set('showConfirmModal', true)"
                                    class="ml-4 text-red-600 hover:text-red-800 text-xs font-semibold">
                                    [Hapus]
                                </button>
                            </div>
                            @endif

                            <x-input-error :messages="$errors->get('materialForm.document')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="document_link" value="{{ __('profile.link_doc_external')}}" />
                            <x-text-input wire:model="materialForm.document_link" id="document_link" type="url" class="mt-1 block w-full" />
                            <p class="mt-1 text-xs text-gray-500">{{ __('profile.public_link')}} .</p>
                            <x-input-error :messages="$errors->get('materialForm.document_link')" class="mt-2" />
                        </div>
                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('profile.save') }}</x-primary-button>
                            <x-action-message class="me-3" on="saved-materi">{{ __('profile.saved') }}</x-action-message>
                        </div>
                    </form>
                </section>
            </div>

            {{-- CARD 5: KEAMANAN --}}
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <section>
                    <header>
                        <h2 class="text-lg font-medium text-gray-900">{{ __('profile.change_password')}}</h2>
                        <p class="mt-1 text-sm text-gray-600">{{ __('profile.change_password_desc')}}.</p>
                    </header>
                    <form wire:submit.prevent="updatePassword" class="mt-6 space-y-6">
                        <div><x-input-label for="current_password" value="{{ __('profile.current_password')}}" /><x-text-input wire:model="passwordForm.current_password" id="current_password" type="password" class="mt-1 block w-full" /><x-input-error :messages="$errors->get('passwordForm.current_password')" class="mt-2" /></div>
                        <div><x-input-label for="password" value="{{ __('profile.new_password')}}" /><x-text-input wire:model="passwordForm.password" id="password" type="password" class="mt-1 block w-full" /><x-input-error :messages="$errors->get('passwordForm.password')" class="mt-2" /></div>
                        <div><x-input-label for="password_confirmation" value="{{ __('profile.confirm_password')}}" /><x-text-input wire:model="passwordForm.password_confirmation" id="password_confirmation" type="password" class="mt-1 block w-full" /><x-input-error :messages="$errors->get('passwordForm.password_confirmation')" class="mt-2" /></div>
                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('profile.save') }}</x-primary-button>
                            <x-action-message class="me-3" on="saved-password">{{ __('profile.saved') }}</x-action-message>
                        </div>
                    </form>
                </section>
            </div>

        </div>
    </div>

    {{-- Letakkan kode ini sebelum tag penutup </div> paling akhir --}}
    <div
        x-data="{ show: @entangle('showConfirmModal').live }"
        x-show="show"
        x-on:keydown.escape.window="show = false"
        class="fixed inset-0 z-50 overflow-y-auto"
        aria-labelledby="modal-title"
        role="dialog"
        aria-modal="true"
        style="display: none;">
        <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-lg">
                <div class="sm:flex sm:items-start">
                    <div class="flex items-center justify-center flex-shrink-0 w-12 h-12 mx-auto bg-red-100 rounded-full sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="w-6 h-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg font-medium leading-6 text-gray-900" id="modal-title">{{ __('profile.delete_logo') }}</h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">{{ __('profile.delete_logo_confirmation') }}</p>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                    <button wire:click="executeDeletion" type="button" class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-red-600 border border-transparent rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        {{ __('profile.yes_delete') }}
                    </button>
                    <button @click="show = false" type="button" class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
                        {{ __('profile.cancel') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>


{{-- ====================================================== --}}
{{-- ==> SCRIPT UNTUK ONBOARDING TOUR <== --}}
{{-- ====================================================== --}}
@push('scripts')
<script>
    function startTour(force = false) {
        // Cek jika tur sudah pernah dilihat, jangan mulai otomatis
        if (localStorage.getItem('editProfileTourCompleted') && !force) {
            return;
        }

        const tour = new Shepherd.Tour({
            defaultStepOptions: {
                classes: 'shepherd-custom', // Menggunakan class kustom dari app.css
                scrollTo: {
                    behavior: 'smooth',
                    block: 'center'
                },
                cancelIcon: {
                    enabled: true
                }
            }
        });

        // Langkah 1: Informasi Instansi
        tour.addStep({
            title: 'Informasi Utama',
            text: 'Pada bagian ini, silakan unggah logo dan isi nama serta deskripsi singkat instansi atau perusahaan Anda.',
            attachTo: {
                element: '#tour-step-1',
                on: 'bottom'
            },
            buttons: [{
                text: 'Lanjut',
                action: tour.next
            }]
        });

        // Langkah 2: Kontak Tambahan
        tour.addStep({
            title: 'Kontak Tambahan',
            text: 'Terakhir, isi informasi kontak tambahan yang akan ditampilkan kepada pengunjung di profil publik Anda.',
            attachTo: {
                element: '#tour-step-2',
                on: 'bottom'
            },
            buttons: [{
                    text: 'Kembali',
                    secondary: true,
                    action: tour.back
                },
                {
                    text: 'Lanjut',
                    action: tour.next
                }
            ]
        });

        // Langkah 3: Media Sosial
        tour.addStep({
            title: 'Tautan Media Sosial',
            text: 'Isi semua tautan media sosial dan website Anda agar pengunjung dapat dengan mudah menemukan Anda.',
            attachTo: {
                element: '#tour-step-3',
                on: 'bottom'
            },
            buttons: [{
                    text: 'Kembali',
                    secondary: true,
                    action: tour.back
                },
                {
                    text: 'Lanjut',
                    action: tour.next
                }
            ]
        });


        // Langkah 4: Materi Promosi
        tour.addStep({
            title: 'Materi Promosi',
            text: 'Anda dapat mengunggah dokumen (seperti brosur) atau menempelkan tautan ke materi promosi Anda di sini.',
            attachTo: {
                element: '#tour-step-4',
                on: 'bottom'
            },
            buttons: [{
                    text: 'Kembali',
                    secondary: true,
                    action: tour.back
                },
                {
                    text: 'Selesai!',
                    action: tour.complete
                }
            ]
        });

        // Saat tur selesai atau dibatalkan, tandai di localStorage
        tour.on('complete', () => localStorage.setItem('editProfileTourCompleted', 'true'));
        tour.on('cancel', () => localStorage.setItem('editProfileTourCompleted', 'true'));

        tour.start();
    }

    // Memulai tur secara otomatis hanya pada kunjungan pertama
    document.addEventListener('DOMContentLoaded', () => {
        startTour(false);
    });
</script>
@endpush