@props(['wire:model'])

<div wire:ignore x-data="{
    signaturePad: null,
    init() {
        const canvas = this.$refs.canvas;
        this.signaturePad = new SignaturePad(canvas);
        canvas.addEventListener('mouseup', () => {
            this.$wire.set('{{ $attributes->wire('model')->value() }}', this.signaturePad.toDataURL('image/png'));
        });
    },
    clear() {
        this.signaturePad.clear();
        this.$wire.set('{{ $attributes->wire('model')->value() }}', null);
    }
}">
    <canvas x-ref="canvas" class="border border-gray-300 rounded-md w-full"></canvas>
    <button type="button" @click="clear()" class="mt-2 px-3 py-1 border border-gray-300 rounded-md text-xs text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
        Clear Signature
    </button>
</div>