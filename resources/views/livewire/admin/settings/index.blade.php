<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Application Settings') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    @if (session()->has('message'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline">{{ session('message') }}</span>
                    </div>
                    @endif

                    <form wire:submit.prevent="save">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <div class="md:col-span-2">
                                <h3 class="text-lg font-medium text-gray-900">General Settings</h3>
                                <p class="mt-1 text-sm text-gray-600">Update your application's general details.</p>
                            </div>

                            <div class="md:col-span-2">
                                <label for="appName" class="block text-sm font-medium text-gray-700">Application Name</label>
                                <input type="text" id="appName" wire:model.defer="appName" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                @error('appName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="contactEmail" class="block text-sm font-medium text-gray-700">Contact Email</label>
                                <input type="email" id="contactEmail" wire:model.defer="contactEmail" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                @error('contactEmail') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div class="md:col-span-2 mt-6">
                                <h3 class="text-lg font-medium text-gray-900">Branding</h3>
                                <p class="mt-1 text-sm text-gray-600">Upload your application logo and favicon.</p>
                            </div>

                            <div>
                                <label for="newLogo" class="block text-sm font-medium text-gray-700">Application Logo</label>
                                <input type="file" id="newLogo" wire:model="newLogo" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 border border-gray-300 rounded-lg p-2">
                                @if ($appLogo && !$newLogo)
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $appLogo) }}" alt="Current Logo" class="h-16">
                                </div>
                                @endif
                                <div wire:loading wire:target="newLogo" class="mt-2 text-sm text-gray-500">Uploading...</div>
                                @error('newLogo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="newFavicon" class="block text-sm font-medium text-gray-700">Favicon (.ico, .png)</label>
                                <input type="file" id="newFavicon" wire:model="newFavicon" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 border border-gray-300 rounded-lg p-2">
                                @if ($appFavicon && !$newFavicon)
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $appFavicon) }}" alt="Current Favicon" class="h-8 w-8">
                                </div>
                                @endif
                                <div wire:loading wire:target="newFavicon" class="mt-2 text-sm text-gray-500">Uploading...</div>
                                @error('newFavicon') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div class="md:col-span-2 mt-6">
                                <h3 class="text-lg font-medium text-gray-900">SEO Meta Tags</h3>
                                <p class="mt-1 text-sm text-gray-600">Configure meta tags for better search engine visibility.</p>
                            </div>

                            <div>
                                <label for="metaTitle" class="block text-sm font-medium text-gray-700">Meta Title</label>
                                <input type="text" id="metaTitle" wire:model.defer="metaTitle" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                @error('metaTitle') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="metaKeywords" class="block text-sm font-medium text-gray-700">Meta Keywords</label>
                                <input type="text" id="metaKeywords" wire:model.defer="metaKeywords" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="e.g., event, seminar, technology">
                                @error('metaKeywords') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label for="metaDescription" class="block text-sm font-medium text-gray-700">Meta Description</label>
                                <textarea id="metaDescription" wire:model.defer="metaDescription" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"></textarea>
                                @error('metaDescription') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            {{-- ====================================================== --}}
                            {{-- PENGATURAN EMAIL SMTP --}}
                            {{-- ====================================================== --}}
                            <div class="md:col-span-2 mt-6 pt-6 border-t border-gray-200">
                                <h3 class="text-lg font-medium text-gray-900">Mail Settings (SMTP)</h3>
                                <p class="mt-1 text-sm text-gray-600">Konfigurasi server untuk mengirim email dari aplikasi.</p>

                                {{-- Notifikasi Sukses/Error --}}
                                @if (session()->has('mail_success'))
                                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mt-4" role="alert">
                                    <span class="block sm:inline">{{ session('mail_success') }}</span>
                                </div>
                                @endif
                                @if (session()->has('mail_error'))
                                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mt-4" role="alert">
                                    <strong class="font-bold">Error!</strong>
                                    <span class="block sm:inline">{{ session('mail_error') }}</span>
                                </div>
                                @endif

                                {{-- Input dan Tombol Kirim Email Percobaan --}}
                                <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                                    <div class="md:col-span-2">
                                        <label for="testEmailRecipient" class="block text-sm font-medium text-gray-700">Kirim Email Percobaan Ke</label>
                                        <input type="email" id="testEmailRecipient" wire:model.defer="testEmailRecipient" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" placeholder="recipient@example.com">
                                        @error('testEmailRecipient') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <button type="button" wire:click="sendTestEmail" wire:loading.attr="disabled" class="w-full inline-flex items-center justify-center px-4 py-3 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                            <span wire:loading.remove wire:target="sendTestEmail">Kirim Email</span>
                                            <span wire:loading wire:target="sendTestEmail">Mengirim...</span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label for="mailHost" class="block text-sm font-medium text-gray-700">Mail Host</label>
                                <input type="text" id="mailHost" wire:model.defer="mailHost" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" placeholder="e.g., smtp.mailgun.org">
                            </div>

                            <div>
                                <label for="mailPort" class="block text-sm font-medium text-gray-700">Mail Port</label>
                                <input type="text" id="mailPort" wire:model.defer="mailPort" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" placeholder="e.g., 587">
                            </div>

                            <div>
                                <label for="mailUsername" class="block text-sm font-medium text-gray-700">Username</label>
                                <input type="text" id="mailUsername" wire:model.defer="mailUsername" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>

                            <div>
                                <label for="mailPassword" class="block text-sm font-medium text-gray-700">Password</label>
                                <input type="password" id="mailPassword" wire:model.defer="mailPassword" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>

                            <div>
                                <label for="mailEncryption" class="block text-sm font-medium text-gray-700">Encryption</label>
                                <select id="mailEncryption" wire:model.defer="mailEncryption" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    <option value="tls">TLS</option>
                                    <option value="ssl">SSL</option>
                                    <option value="">None</option>
                                </select>
                            </div>

                            <div>
                                <label for="mailFromAddress" class="block text-sm font-medium text-gray-700">From Address</label>
                                <input type="email" id="mailFromAddress" wire:model.defer="mailFromAddress" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" placeholder="e.g., no-reply@example.com">
                            </div>

                            <div class="md:col-span-2">
                                <label for="mailFromName" class="block text-sm font-medium text-gray-700">From Name</label>
                                <input type="text" id="mailFromName" wire:model.defer="mailFromName" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" placeholder="e.g., AIGIS 2026">
                            </div>
                        </div>

                        <div class="md:col-span-2 mt-10 pt-6 border-t border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Footer Settings</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                                <div>
                                    <label for="newFooterLogo" class="block text-sm font-medium text-gray-700">Footer Logo</label>
                                    <input type="file" id="newFooterLogo" wire:model="newFooterLogo" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 border border-gray-300 rounded-lg p-2">
                                    {{-- Tampilkan logo yang ada --}}
                                    @if ($footerLogo && !$newFooterLogo)
                                    <div class="mt-2">
                                        <img src="{{ asset('storage/' . $footerLogo) }}" alt="Current Footer Logo" class="h-16">
                                    </div>
                                    @endif
                                    <div wire:loading wire:target="newFooterLogo" class="mt-2 text-sm text-gray-500">Uploading...</div>
                                    @error('newFooterLogo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label for="footerEmail" class="block text-sm font-medium text-gray-700">Contact Email</label>
                                    <input type="email" wire:model.defer="footerEmail" id="footerEmail" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    @error('footerEmail') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label for="footerPhone" class="block text-sm font-medium text-gray-700">Contact Phone</label>
                                    <input type="text" wire:model.defer="footerPhone" id="footerPhone" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    @error('footerPhone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label for="footerWhatsapp" class="block text-sm font-medium text-gray-700">WhatsApp Number</label>
                                    <input type="text" wire:model.defer="footerWhatsapp" id="footerWhatsapp" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" placeholder="e.g., +628123456789">
                                    @error('footerWhatsapp') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label for="footerFacebookUrl" class="block text-sm font-medium text-gray-700">Facebook URL</label>
                                    <input type="url" wire:model.defer="footerFacebookUrl" id="footerFacebookUrl" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    @error('footerFacebookUrl') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label for="footerInstagramUrl" class="block text-sm font-medium text-gray-700">Instagram URL</label>
                                    <input type="url" wire:model.defer="footerInstagramUrl" id="footerInstagramUrl" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    @error('footerInstagramUrl') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label for="footer_wikipedia_url" class="block text-sm font-medium text-gray-700">Wikipedia URL</label>
                                    <input type="url" wire:model.defer="footerWikipediaUrl" id="footer_wikipedia_url" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    @error('footerWikipediaUrl') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label for="footerYoutubeUrl" class="block text-sm font-medium text-gray-700">YouTube URL</label>
                                    <input type="url" wire:model.defer="footerYoutubeUrl" id="footerYoutubeUrl" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    @error('footerYoutubeUrl') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>


                        <div class="flex items-center justify-end mt-6">
                            <button type="submit" wire:loading.attr="disabled" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                <span wire:loading.remove>Save Settings</span>
                                <span wire:loading>Saving...</span>
                            </button>
                        </div>
                    </form>
                </div>
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class_alias(name)>
                        <div class="mt-8 pt-8 border-t border-gray-200">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                Manajemen Cache
                            </h3>
                            <p class="mt-1 text-sm text-gray-600">
                                Tindakan ini akan menghapus file cache aplikasi, view, route, dan konfigurasi. Ini berguna jika perubahan terbaru tidak muncul di situs.
                            </p>

                            @if (session()->has('cache_success'))
                            <div class="mt-4 p-4 bg-green-100 text-green-700 rounded-md">
                                {{ session('cache_success') }}
                            </div>
                            @endif
                            @if (session()->has('cache_error'))
                            <div class="mt-4 p-4 bg-red-100 text-red-700 rounded-md">
                                {{ session('cache_error') }}
                            </div>
                            @endif

                            <div class="mt-5">
                                <x-danger-button wire:click="clearCache" wire:loading.attr="disabled">
                                    <span wire:loading.remove wire:target="clearCache">
                                        <svg class="w-5 h-5 mr-2 -ml-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                        </svg>
                                        Bersihkan Cache Aplikasi Sekarang
                                    </span>

                                    <span wire:loading wire:target="clearCache">
                                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Sedang Membersihkan...
                                    </span>
                                </x-danger-button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>