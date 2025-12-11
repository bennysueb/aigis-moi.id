<x-guest-layout>
    <div class="min-h-screen bg-gray-100 py-8 px-4 sm:px-6 lg:px-8 flex flex-col items-center">

        {{-- Container Surat (Style Kertas A4) --}}
        {{-- PERUBAHAN: Menambahkan background-image inline style --}}
        <div class="w-full max-w-[210mm] min-h-[297mm] bg-white shadow-lg sm:rounded-lg overflow-hidden relative bg-top bg-no-repeat bg-contain"
            style="@if($event->invitation_letter_header) background-image: url('{{ asset('storage/' . $event->invitation_letter_header) }}'); @endif">

            {{-- Spacer agar teks tidak menabrak header (Kop Surat) --}}
            {{-- Sesuaikan height (h-32, h-48) dengan tinggi gambar kop surat Anda --}}
            <div class="w-full h-48 md:h-56"></div>

            {{-- Isi Surat (Overlay di atas background) --}}
            <div class="relative z-10 px-8 pb-12 sm:px-12">

                {{-- Konten dari CKEditor --}}
                <div class="prose max-w-none text-gray-800 leading-relaxed font-serif">
                    {!! $content !!}
                </div>



            </div>
        </div>

        {{-- Area Lampiran (Jika ada) --}}
        @if(!empty($event->invitation_files))
        <div class="w-full max-w-[210mm] mt-12 pt-6 border-t border-gray-200 bg-white/80 backdrop-blur-sm rounded-lg p-4">
            <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-3">Dokumen Lampiran</h4>
            <div class="grid grid-cols-1 gap-2">
                @foreach($event->invitation_files as $filePath)
                <a href="{{ asset('storage/' . $filePath) }}" target="_blank" class="flex items-center p-3 bg-white border border-gray-200 rounded-md hover:bg-blue-50 hover:border-blue-300 transition-colors group">
                    <span class="text-2xl mr-3">📄</span>
                    <div class="flex-grow">
                        <p class="text-sm font-medium text-gray-700 group-hover:text-blue-700">
                            {{ basename($filePath) }}
                        </p>
                        <p class="text-xs text-gray-400">Klik untuk mengunduh</p>
                    </div>
                    <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                </a>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Tombol Aksi Floating --}}
        <div class="fixed bottom-0 left-0 right-0 p-4 bg-white border-t shadow-lg sm:static sm:bg-transparent sm:border-none sm:shadow-none sm:mt-6 sm:text-center z-50">
            <div class="max-w-[210mm] mx-auto flex flex-col sm:flex-row gap-3 justify-center">
                <a href="{{ route('invitation.confirm', $invitation->uuid) }}" class="inline-flex justify-center items-center px-6 py-3 bg-blue-600 border border-transparent rounded-md font-semibold text-base text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-md">
                    Ke Halaman Konfirmasi
                </a>
            </div>
        </div>

        <div class="h-20 sm:hidden"></div>
    </div>
</x-guest-layout>