@props(['fields'])

<div class="space-y-6">
    @foreach($fields as $field)
    <div>
        <label for="{{ $field['name'] }}" class="block text-sm font-medium text-secondary-dark">
            {{ $field['label'] }} @if($field['required'] ?? false) <span class="text-red-500">*</span> @endif
        </label>

        @if($field['type'] === 'textarea')
        <textarea wire:model.defer="formData.{{ $field['name'] }}" id="{{ $field['name'] }}" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"></textarea>
        @elseif($field['type'] === 'select')
        <select wire:model.defer="formData.{{ $field['name'] }}" id="{{ $field['name'] }}" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            <option value="">Choose an option</option>
            @foreach($field['options'] as $option)
            <option value="{{ $option }}">{{ $option }}</option>
            @endforeach
        </select>
        @elseif($field['type'] === 'radio')
        <div class="mt-2 space-y-2">
            @foreach($field['options'] as $option)
            <label class="inline-flex items-center"><input type="radio" wire:model.defer="formData.{{ $field['name'] }}" value="{{ $option }}" class="rounded-full border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"><span class="ml-2 text-sm">{{ $option }}</span></label>
            @endforeach
        </div>
        @elseif($field['type'] === 'checkbox')
        <div class="mt-2"><label class="inline-flex items-center"><input type="checkbox" wire:model.defer="formData.{{ $field['name'] }}" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"><span class="ml-2 text-sm">{{ $field['label'] }}</span></label></div>
        @elseif(in_array($field['type'], ['file', 'image']))
        <input type="file" wire:model="formData.{{ $field['name'] }}" id="{{ $field['name'] }}" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
        <div wire:loading wire:target="formData.{{ $field['name'] }}">Uploading...</div>
        @elseif($field['type'] === 'signature')
        <div class="mt-1"><x-signature-pad wire:model="formData.{{ $field['name'] }}" id="{{ $field['name'] }}"></x-signature-pad></div>
        @else
        <input type="{{ $field['type'] }}" wire:model.defer="formData.{{ $field['name'] }}" id="{{ $field['name'] }}" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
        @endif
        @error('formData.' . $field['name']) <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
    </div>
    @endforeach
</div>