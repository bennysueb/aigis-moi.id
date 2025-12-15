<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Livewire Test Page
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-3xl font-bold">{{ $count }}</h1>
                    <button wire:click="increment" type="button" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-md">
                        + Increment
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>