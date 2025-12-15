<div>
    {{-- Ini adalah div pembungkus yang wajib ada --}}

    @if($showModal)
    <div class="fixed z-50 inset-0 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen">
            <div class="fixed inset-0 bg-gray-500 opacity-75" wire:click="$set('showModal', false)"></div>
            <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-6xl sm:w-full">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-2xl font-bold">Select Media</h3>
                        <button wire:click="$set('showModal', false)" class="text-2xl">&times;</button>
                    </div>

                    <div class="flex space-x-4 h-[70vh]">
                        <aside class="w-1/4 border-r pr-4 overflow-y-auto">
                            <h4 class="text-lg font-bold mb-2">Albums</h4>
                            @if($albums)
                            <ul>
                                @foreach($albums as $album)
                                <li wire:click="selectAlbum({{ $album->id }})" class="p-2 rounded cursor-pointer {{ optional($selectedAlbum)->id == $album->id ? 'bg-gray-200' : 'hover:bg-gray-100' }}">
                                    {{ $album->name }} ({{ $album->media->count() }})
                                </li>
                                @endforeach
                            </ul>
                            @endif
                        </aside>

                        <main class="w-3/4 overflow-y-auto">
                            @if($selectedAlbum)
                            <div class="grid grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                                @foreach($selectedAlbum->getMedia() as $media)
                                <div wire:click="selectMedia({ url: '{{ $media->getUrl() }}' })" class="cursor-pointer group">
                                    <img src="{{ $media->getUrl('thumbnail') }}" alt="{{ $media->name }}" class="w-full h-32 object-cover rounded-lg group-hover:ring-2 ring-blue-500">
                                </div>
                                @endforeach
                            </div>
                            @endif
                        </main>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>