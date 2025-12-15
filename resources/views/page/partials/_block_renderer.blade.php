@props(['block'])

@php
// 1. Cari template berdasarkan slug
$template = \App\Models\SectionTemplate::where('slug', $block['template_slug'])->first();
@endphp

@if($template)
    @php
        $content = $block['data'];
        
        // Ambil konten HTML dan CSS asli dari database
        $renderedHtml = $template->html_content;
        $renderedCss  = $template->css_content;

        // 2. Loop melalui definisi field untuk replace variabel {{ $name }}
        if(is_array($template->fields)) {
            foreach ($template->fields as $field) {
                $key = $field['name'];
                $placeholder = "{{ \$$key }}";

                // SKIP: Jika tipe repeater, jangan di-replace manual (biarkan Blade yang handle array-nya)
                if ($field['type'] === 'repeater') {
                    continue; 
                }

                // Cek ketersediaan data
                if (array_key_exists($key, $content)) {
                    $value = $content[$key];

                    // SAFETY: Jangan replace jika value adalah array/object
                    if (is_array($value) || is_object($value)) {
                        continue;
                    }

                    // Tentukan cara replace berdasarkan tipe field
                    if ($field['type'] === 'text') {
                        // Text: Escape html untuk keamanan
                        $replacement = e($value);
                    } else {
                        // Textarea, Image, Link: Biarkan raw (apa adanya)
                        $replacement = $value;
                    }

                    // Lakukan replacement di HTML dan CSS
                    $renderedHtml = str_replace($placeholder, $replacement, $renderedHtml);
                    $renderedCss  = str_replace($placeholder, $replacement, $renderedCss);

                } else {
                    // Jika data kosong, ganti placeholder dengan string kosong
                    $renderedHtml = str_replace($placeholder, '', $renderedHtml);
                    $renderedCss  = str_replace($placeholder, '', $renderedCss);
                }
            }
        }
    @endphp

    {{-- 3. Render CSS (Style Block) jika ada isinya --}}
    @if(!empty($renderedCss))
        <style>
            {!! $renderedCss !!}
        </style>
    @endif

    {{-- 4. Render HTML dengan Blade Engine (Support @foreach, @if, dll) --}}
    {!! \Illuminate\Support\Facades\Blade::render($renderedHtml, $content) !!}

@endif