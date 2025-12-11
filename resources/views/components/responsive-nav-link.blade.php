@props(['active'])

@php
// DIUBAH: Logika class disesuaikan untuk tema gelap dan warna text-white
$classes = ($active ?? false)
? 'block w-full ps-3 pe-4 py-2 border-l-4 border-primary text-white bg-gray-700/50 focus:outline-none focus:text-white focus:bg-gray-600 focus:border-accent transition duration-150 ease-in-out'
: 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-base font-medium text-white hover:text-greener hover:bg-primary focus:outline-none focus:text-white focus:bg-gray-700 focus:border-gray-600 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>