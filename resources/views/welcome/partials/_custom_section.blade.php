@props(['section'])

{{--
    Relasi: 
    $section (WelcomeSection) -> customSection (CustomSection) -> template (SectionTemplate) 
--}}
@if($section->customSection && $section->customSection->template)
@php
$template = $section->customSection->template;
// $content sekarang adalah array multi-bahasa: ['en' => [...], 'id' => [...]]
$allContent = $section->customSection->content;
$renderedHtml = $template->html_content;

// ==========================================================
// PERUBAHAN DI SINI: Ambil locale & data yang sesuai
// ==========================================================
$currentLocale = app()->getLocale();
// Ambil data untuk locale saat ini, fallback ke 'en' jika tidak ada
$localeContent = $allContent[$currentLocale] ?? ($allContent['en'] ?? []);

// Loop fields dari template
foreach ($template->fields as $field) {
$key = $field['name'];
$placeholder = "{{ \$$key }}";

// Cek jika datanya ada DI DALAM $localeContent
if (array_key_exists($key, $localeContent)) {
$value = $localeContent[$key]; // Ambil nilai dari locale yang benar

// Terapkan logika escape berdasarkan TIPE field (Ini sudah benar)
if ($field['type'] === 'text') {
$renderedHtml = str_replace($placeholder, e($value), $renderedHtml);
} elseif ($field['type'] === 'textarea') {
$renderedHtml = str_replace($placeholder, $value, $renderedHtml); // No escape for HTML
} else { // Image & Link
$renderedHtml = str_replace($placeholder, $value, $renderedHtml); // No escape for URLs
}

} else {
// Jika datanya tidak ada di locale ini, ganti placeholder dengan string kosong
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

{{-- Render HTML yang sudah digabungkan (tidak berubah) --}}
{!! $renderedHtml !!}

@endif