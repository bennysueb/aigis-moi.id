<div class="flex items-center space-x-4">
    @foreach (config('languages') as $locale => $label)
    <a href="#" wire:click.prevent="setLocale('{{ $locale }}')"
        class="flex items-center space-x-2 opacity-80 hover:opacity-100 transition-opacity duration-200">

        {{-- Logika untuk menampilkan bendera --}}
        @if ($locale == 'en')
        {{-- Bendera United Kingdom (untuk English) --}}
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 60 30" class="w-6 h-auto rounded-sm shadow-md border border-black">
            <clipPath id="s">
                <path d="M0,0 v30 h60 v-30 z" />
            </clipPath>
            <clipPath id="t">
                <path d="M30,15 h30 v15 z v15 h-30 z h-30 v-15 z v-15 h30 z" />
            </clipPath>
            <g clip-path="url(#s)">
                <path d="M0,0 v30 h60 v-30 z" fill="#00247d" />
                <path d="M0,0 L60,30 M60,0 L0,30" stroke="#fff" stroke-width="6" />
                <path d="M0,0 L60,30 M60,0 L0,30" clip-path="url(#t)" stroke="#cf142b" stroke-width="4" />
                <path d="M30,0 v30 M0,15 h60" stroke="#fff" stroke-width="10" />
                <path d="M30,0 v30 M0,15 h60" stroke="#cf142b" stroke-width="6" />
            </g>
        </svg>
        @elseif ($locale == 'id')
        {{-- Bendera Indonesia --}}
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 9 6" class="w-6 h-auto rounded-sm shadow-md border border-black">
            <path fill="#fff" d="M0 0h9v6H0z" />
            <path fill="#ce1126" d="M0 0h9v3H0z" />
        </svg>
        @endif

        <span :class="scrolled ? 'text-greener' : 'text-gray-800'" class="{{ session('locale', config('app.locale')) == $locale ? 'font-bold underline' : '' }}">
            {{ strtoupper($locale) }}
        </span>
    </a>
    @endforeach
</div>