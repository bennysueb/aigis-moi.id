@props(['wire:model'])

<div
    wire:ignore
    x-data="{
        value: @entangle($attributes->wire('model'))
    }"
    x-init="
        tinymce.init({
            target: $refs.tinymce,
            selector: '#{{ $attributes->get('id') }}',
            themes: 'modern',
            height: 250,
            menubar: false,
            plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table paste code help wordcount',
            toolbar: 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | link image media | help',
            file_picker_types: 'image media',
            file_picker_callback: function (callback, value, meta) {
                Livewire.dispatch('open-media-modal', { callback: callback });
            },
            setup: function (editor) {
                editor.on('blur', function (e) {
                    value = editor.getContent()
                });
            }
        });

        // TAMBAHKAN PENGAWAS INI
        $watch('value', (newValue) => {
            if (tinymce.get('{{ $attributes->get('id') }}') && newValue !== tinymce.get('{{ $attributes->get('id') }}').getContent()) {
                tinymce.get('{{ $attributes->get('id') }}').setContent(newValue);
            }
        });
    ">
    <textarea id="{{ $attributes->get('id') }}" {{ $attributes->whereDoesntStartWith('wire:model') }}></textarea>
</div>