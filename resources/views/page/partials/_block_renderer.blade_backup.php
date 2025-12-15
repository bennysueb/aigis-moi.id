@props(['block'])

@php
// Cari template berdasarkan slug yang tersimpan di blok
$template = \App\Models\SectionTemplate::where('slug', $block['template_slug'])->first();
@endphp

@if($template)
@php
$content = $block['data'];
$renderedHtml = $template->html_content;

// Loop melalui definisi field di template
foreach ($template->fields as $field) {
$key = $field['name'];
$placeholder = "{{ \$$key }}";

// Cek jika datanya ada
if (array_key_exists($key, $content)) {
$value = $content[$key];

// Terapkan logika berdasarkan TIPE field
if ($field['type'] === 'text') {
// Tipe Text: Selalu escape
$renderedHtml = str_replace($placeholder, e($value), $renderedHtml);

} elseif ($field['type'] === 'textarea') {
// ==========================================================
// PERBAIKAN DI SINI:
// Tipe Textarea sekarang dianggap sebagai HTML mentah
// dan TIDAK AKAN di-escape.
// ==========================================================
$renderedHtml = str_replace($placeholder, $value, $renderedHtml);

} else {
// Tipe Image & Link: JANGAN di-escape
$renderedHtml = str_replace($placeholder, $value, $renderedHtml);
}

} else {
// Jika datanya tidak ada, ganti placeholder dengan string kosong
$renderedHtml = str_replace($placeholder, '', $renderedHtml);
}
}
@endphp


@if(!empty($template->css_content))
@push('custom_styles')
<style>
    @php echo $template->css_content;
    @endphp
</style>
@endpush
@endif

{{-- Render HTML yang sudah digabungkan --}}
{!! $renderedHtml !!}
@endif