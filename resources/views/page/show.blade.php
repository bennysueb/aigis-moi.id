<x-guest-layout>


    <!-- <h1 class="text-4xl font-bold font-heading mb-4">{{ $page->getTranslation('title', app()->getLocale()) }}</h1> -->


    @php
    // Ambil konten untuk locale yang sedang aktif
    $content = $page->getTranslation('content', app()->getLocale());
    @endphp

    {{-- Cek apakah kontennya adalah array (dari Page Builder) --}}
    @if(is_array($content))
    {{-- Loop melalui setiap blok dan panggil "mesin render" --}}
    @foreach($content as $block)
    @include('page.partials._block_renderer', ['block' => $block])
    @endforeach
    @else
    {{-- Jika konten adalah string biasa, cetak langsung --}}
    {!! $content !!}
    @endif



</x-guest-layout>