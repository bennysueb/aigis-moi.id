<x-guest-layout>
    @if(isset($sections))
    @foreach($sections as $section)
    @if($section->custom_section_id)
    {{-- Render Custom Section jika ada custom_section_id --}}
    @include('welcome.partials._custom_section', ['section' => $section])
    @elseif($section->component)
    {{-- Fallback ke cara lama jika itu adalah section bawaan --}}
    @include('welcome.partials._' . $section->component, ['items' => $data[$section->component] ?? []])
    @endif
    @endforeach
    @endif
</x-guest-layout>