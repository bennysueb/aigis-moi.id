@props(['wire:model'])

<div
    wire:ignore
    {{ $attributes }}
    x-data="{
        value: @entangle($attributes->wire('model'))
    }"
    x-init="
        ClassicEditor
            .create($el.querySelector('textarea'), { // Inisialisasi pada textarea di dalam div
                toolbar: {
                    items: [
                        'heading', '|',
                        'bold', 'italic', 'underline', '|',
                        'bulletedList', 'numberedList', '|',
                        'blockQuote', 'insertTable', 'link', '|',
                        'undo', 'redo'
                    ]
                }
            })
            .then(editor => {
                // Atur data awal dari Livewire
                editor.setData(value);

                // Saat ada perubahan di editor, update nilai Alpine (dan Livewire)
                editor.model.document.on('change:data', () => {
                    value = editor.getData();
                })

                // Saat nilai dari Livewire berubah (misal: load template), update editor
                $watch('value', (newValue) => {
                    if (newValue !== editor.getData()) {
                        editor.setData(newValue);
                    }
                });
            })
            .catch(error => {
                console.error(error);
            });
    ">
    <textarea x-ref="ckeditor" class="hidden"></textarea>
</div>