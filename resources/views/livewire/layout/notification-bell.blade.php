<div class="relative" x-data="{ open: false }">
    <button @click="open = !open" class="relative text-white hover:text-accent p-2 rounded-full focus:outline-none">
        {{-- Ikon Lonceng --}}
        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
        {{-- Titik Merah Notifikasi --}}
        @if($unreadCount > 0)
        <span class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-red-500 ring-2 ring-white"></span>
        @endif
    </button>

    {{-- Dropdown Notifikasi --}}
    <div x-show="open" @click.away="open = false"
        class="origin-top-right absolute right-0 mt-2 w-80 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50"
        x-transition>
        <div class="py-1">
            <div class="px-4 py-2 text-sm text-gray-700 font-bold border-b">Notifications</div>
            @forelse($unreadNotifications as $notification)
            <button wire:click="markAsRead('{{ $notification->id }}')"
                class="w-full text-left block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                {{ $notification->data['message'] }}
            </button>
            @empty
            <p class="px-4 py-3 text-sm text-gray-500">No new notifications.</p>
            @endforelse
        </div>
    </div>
</div>