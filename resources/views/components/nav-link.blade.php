@props(['active', 'scrolled' => true])

@php
// Base classes untuk semua link
$baseClasses = 'relative inline-flex items-center px-3 py-1 text-sm font-medium leading-5 focus:outline-none transition duration-150 ease-in-out rounded-t-md h-full';

// Classes untuk pseudo-element (garis bawah)
$underlineClasses = "after:content-[''] after:absolute after:bottom-0 after:left-0 after:w-full after:h-[2px] after:transition-transform after:duration-300";

// Tentukan warna berdasarkan state 'scrolled'
$inactiveColor = $scrolled ? 'text-gray-300 hover:text-white' : 'text-gray-700 hover:text-black';
$activeColor = $scrolled ? 'text-white' : 'text-black';
$underlineColor = $scrolled ? 'after:bg-green-default' : 'after:bg-gray-800';

$classes = ($active ?? false)
? $baseClasses . ' ' . $activeColor . ' ' . $underlineClasses . ' ' . $underlineColor . ' after:scale-x-100'
: $baseClasses . ' ' . $inactiveColor . ' ' . $underlineClasses . ' ' . $underlineColor . ' after:scale-x-0 hover:after:scale-x-100';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>