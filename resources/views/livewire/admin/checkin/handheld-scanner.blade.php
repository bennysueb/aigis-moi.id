<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Handheld Scanner: <span class="font-bold">{{ $event->name }}</span>
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 text-center">
                    @if (session()->has('success'))
                    <div class="p-4 mb-4 text-lg font-bold bg-green-100 text-green-800 rounded-md">{{ session('success') }}</div>
                    @endif
                    @if (session()->has('warning'))
                    <div class="p-4 mb-4 text-lg font-bold bg-yellow-100 text-yellow-800 rounded-md">{{ session('warning') }}</div>
                    @endif
                    @if (session()->has('error'))
                    <div class="p-4 mb-4 text-lg font-bold bg-red-100 text-red-800 rounded-md">{{ session('error') }}</div>
                    @endif
                    <form wire:submit.prevent="checkIn">
                        <label for="manualUuid" class="block text-sm font-medium text-gray-700">Scan with Handheld Scanner</label>
                        <input type="text" wire:model="manualUuid" id="manualUuid" placeholder="Click here and scan..."
                            class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md text-center text-lg"
                            autofocus>
                    </form>
                    <p class="mt-4 text-sm text-gray-500">The page will show a status message after each scan.</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:init', () => {
        // Dengarkan sinyal 'refocus-scanner-input' dari backend
        Livewire.on('refocus-scanner-input', () => {
            // Beri jeda sesaat agar Livewire selesai me-render
            setTimeout(() => {
                document.getElementById('manualUuid').focus();
            }, 100);
        });
    });
</script>
@endpush