<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
            
            <div class="flex justify-between mb-4">
                <h2 class="text-xl font-bold text-gray-800">Kelola Agenda Acara</h2>
                <button wire:click="create" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    + Tambah Agenda
                </button>
            </div>

            @if (session()->has('message'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4">
                    {{ session('message') }}
                </div>
            @endif

            <table class="table-auto w-full border-collapse border border-gray-200">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="px-4 py-2 border">Events</th> <th class="px-4 py-2 border">Waktu</th>
                        <th class="px-4 py-2 border">Agenda</th>
                        <th class="px-4 py-2 border">Lokasi/Speaker</th>
                        <th class="px-4 py-2 border">Link</th>
                        <th class="px-4 py-2 border">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($agendas as $agenda)
                    <tr>
                        <td class="border px-4 py-2">
                            @if($agenda->events->isEmpty())
                                <span class="bg-gray-200 text-gray-600 py-1 px-2 rounded text-xs">Umum / Semua Event</span>
                            @else
                                <div class="flex flex-wrap gap-1">
                                    @foreach($agenda->events as $evt)
                                        <span class="bg-blue-100 text-blue-800 py-1 px-2 rounded text-xs">
                                            {{ is_array($evt->name) ? $evt->name['id'] ?? $evt->name['en'] : $evt->name }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </td>
                        <td class="border px-4 py-2">
                            {{ $agenda->start_time->format('d M Y') }}<br>
                            <span class="text-sm text-gray-500">
                                {{ $agenda->start_time->format('H:i') }} - {{ $agenda->end_time->format('H:i') }}
                            </span>
                        </td>
                        <td class="border px-4 py-2">
                            <div class="font-bold">{{ $agenda->title }}</div>
                            <div class="text-xs text-gray-500">{{ Str::limit($agenda->description, 50) }}</div>
                        </td>
                        <td class="border px-4 py-2">
                            <div>📍 {{ $agenda->location ?? '-' }}</div>
                            <div>🎤 {{ $agenda->speaker ?? '-' }}</div>
                        </td>
                        <td class="border px-4 py-2 text-center">
                            @if($agenda->link_url)
                                <a href="{{ $agenda->link_url }}" target="_blank" class="text-blue-500 underline">Link</a>
                            @else
                                -
                            @endif
                        </td>
                        <td class="border px-4 py-2 text-center">
                            <button wire:click="edit({{ $agenda->id }})" class="bg-yellow-500 text-white px-2 py-1 rounded text-sm">Edit</button>
                            <button wire:click="delete({{ $agenda->id }})" onclick="return confirm('Yakin hapus?')" class="bg-red-500 text-white px-2 py-1 rounded text-sm">Hapus</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mt-4">
                {{ $agendas->links() }}
            </div>
        </div>
    </div>

    @if($isOpen)
    <div class="fixed inset-0 flex items-center justify-center z-50 bg-gray-900 bg-opacity-50">
        <div class="bg-white rounded shadow-lg w-1/2 p-6 overflow-y-auto max-h-[90vh]">
            <h2 class="text-xl mb-4 font-bold">{{ $agendaId ? 'Edit Agenda' : 'Tambah Agenda' }}</h2>
            
            <form>
                <div class="mb-4">
                    <label class="block text-sm font-bold mb-2">Pilih Event (Opsional / Bisa Banyak)</label>
                    {{-- MULTIPLE SELECT --}}
                    <select wire:model="event_ids" multiple class="w-full border rounded px-3 py-2 h-32">
                        @foreach($events as $evt)
                            <option value="{{ $evt->id }}">
                                {{ is_array($evt->name) ? $evt->name['id'] ?? $evt->name['en'] : $evt->name }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Tahan tombol <strong>Ctrl</strong> (Windows) atau <strong>Command</strong> (Mac) untuk memilih lebih dari satu. Biarkan kosong jika ini agenda umum.</p>
                    @error('event_ids') <span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-bold mb-2">Judul Agenda</label>
                    <input type="text" wire:model="title" class="w-full border rounded px-3 py-2">
                    @error('title') <span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-bold mb-2">Mulai</label>
                        <input type="datetime-local" wire:model="start_time" class="w-full border rounded px-3 py-2">
                        @error('start_time') <span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold mb-2">Selesai</label>
                        <input type="datetime-local" wire:model="end_time" class="w-full border rounded px-3 py-2">
                        @error('end_time') <span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-bold mb-2">Lokasi</label>
                        <input type="text" wire:model="location" class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-bold mb-2">Pembicara</label>
                        <input type="text" wire:model="speaker" class="w-full border rounded px-3 py-2">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-bold mb-2">Link URL</label>
                    <input type="url" wire:model="link_url" placeholder="https://..." class="w-full border rounded px-3 py-2">
                    @error('link_url') <span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-bold mb-2">Deskripsi</label>
                    <textarea wire:model="description" class="w-full border rounded px-3 py-2"></textarea>
                </div>

                <div class="flex justify-end">
                    <button type="button" wire:click="closeModal" class="bg-gray-500 text-white px-4 py-2 rounded mr-2">Batal</button>
                    <button type="button" wire:click="store" class="bg-blue-600 text-white px-4 py-2 rounded">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>