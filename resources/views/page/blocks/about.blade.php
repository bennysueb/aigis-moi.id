<div class="py-16 bg-white">
    <div class="container mx-auto px-6 md:px-12 xl:px-32">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-800 md:text-4xl">
                {{-- Gunakan '??' untuk memberikan nilai default jika data kosong --}}
                {{ $data['heading'] ?? 'About Our Mission' }}
            </h2>
            <p class="text-gray-600 mt-4">
                {{ $data['eyebrow_text'] ?? 'Learn more about what we do.' }}
            </p>
        </div>
        <div class="grid gap-12 items-center md:grid-cols-2">
            <div class="space-y-6">
                <div class="prose max-w-none">
                    {!! $data['main_content'] ?? '<p>Default content goes here.</p>' !!}
                </div>
            </div>
            <div>
                @if(!empty($data['main_image']))
                <img src="{{ $data['main_image'] }}" alt="Main Image" class="rounded-lg shadow-lg">
                @endif
            </div>
        </div>
    </div>
</div>