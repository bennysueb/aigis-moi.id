<div>
    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Sticky Bar Settings
            </h2>
        </div>
    </header>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form wire:submit.prevent="save">
                <div class="space-y-8">

                    <div class="bg-white shadow-lg rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Link Management</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="getting_there_url" class="block text-sm font-medium text-gray-700">"Getting There" URL</label>
                                <input type="url" wire:model="links.getting_there_url" id="getting_there_url" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="https://maps.google.com/...">
                            </div>
                            <div>
                                <label for="wikipedia_url" class="block text-sm font-medium text-gray-700">Wikipedia URL</label>
                                <input type="url" wire:model="links.wikipedia_url" id="wikipedia_url" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="https://en.wikipedia.org/...">
                            </div>
                            <div>
                                <label for="instagram_url" class="block text-sm font-medium text-gray-700">Instagram URL</label>
                                <input type="url" wire:model="links.instagram_url" id="instagram_url" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="https://instagram.com/...">
                            </div>
                            <div>
                                <label for="youtube_url" class="block text-sm font-medium text-gray-700">YouTube Channel URL</label>
                                <input type="url" wire:model="links.youtube_url" id="youtube_url" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="https://youtube.com/...">
                            </div>
                            <div>
                                <label for="whatsapp_url" class="block text-sm font-medium text-gray-700">WhatsApp URL (wa.me)</label>
                                <input type="url" wire:model="links.whatsapp_url" id="whatsapp_url" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="https://wa.me/62...">
                            </div>
                            <div>
                                <label for="microsite_url" class="block text-sm font-medium text-gray-700">Microsite URL</label>
                                <input type="url" wire:model="links.microsite_url" id="microsite_url" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="https://zlinks.id/...">
                            </div>
                        </div>
                    </div>

                    <div class="bg-white shadow-lg rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Video Gallery Popup</h3>
                        <div>
                            <label for="gallery_title" class="block text-sm font-medium text-gray-700">Gallery Main Title</label>
                            <input type="text" wire:model="galleryTitle" id="gallery_title" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            @error('galleryTitle') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <hr class="my-6">
                        <h4 class="text-md font-medium text-gray-800 mb-4">Video Series</h4>
                        <div class="space-y-4">
                            <div class="mt-2 text-xs text-gray-600 space-y-1">
                                <p><strong>Cara mendapatkan link yang benar:</strong></p>
                                <ol class="list-decimal list-inside pl-2">
                                    <li>Buka video di YouTube, klik <strong>Share</strong> (Bagikan), lalu pilih <strong>Embed</strong> (Sematkan).</li>
                                    <li>Salin URL yang ada di dalam atribut <code>src="..."</code>.</li>
                                    <li>Contoh: <code>https://www.youtube.com/embed/VIDEO_ID</code></li>
                                </ol>
                            </div>
                            @foreach($videos as $index => $video)
                            <div class="flex items-start gap-4 p-4 border rounded-lg" wire:key="video-{{ $index }}">
                                <div class="flex-grow grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="series_title_{{ $index }}" class="block text-sm font-medium text-gray-700">Series Title</label>
                                        <input type="text" wire:model="videos.{{ $index }}.series_title" id="series_title_{{ $index }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                        @error('videos.'.$index.'.series_title') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label for="youtube_embed_url_{{ $index }}" class="block text-sm font-medium text-gray-700">YouTube Embed URL</label>
                                        <input type="url" wire:model="videos.{{ $index }}.youtube_embed_url" id="youtube_embed_url_{{ $index }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                        @error('videos.'.$index.'.youtube_embed_url') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <button wire:click.prevent="removeVideo({{ $index }})" class="mt-6 p-2 text-red-500 hover:text-red-700">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                            @endforeach
                        </div>
                        <button wire:click.prevent="addVideo" class="mt-4 px-4 py-2 bg-blue-500 text-white text-sm font-medium rounded-md hover:bg-blue-600">
                            Add Video
                        </button>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="px-6 py-2 bg-green-600 text-white font-semibold rounded-md shadow-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                            Save Settings
                        </button>
                        <span x-data="{ show: false }" x-show="show" x-transition.opacity.out.duration.1500ms
                            @saved.window="show = true; setTimeout(() => show = false, 2000)"
                            class="ml-3 text-sm text-gray-600 self-center">
                            Saved successfully.
                        </span>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>