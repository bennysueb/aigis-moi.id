<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $user->nama_instansi ?: 'Exhibitor Profile' }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        {{-- Kolom Kiri: Logo & Tombol Aksi --}}
                        <div class="md:col-span-1 space-y-6">
                            @if ($user->logo_path)
                            <img src="{{ asset('storage/' . $user->logo_path) }}" alt="Logo {{ $user->nama_instansi }}" class="w-full h-auto object-contain rounded-lg border p-4">
                            @else
                            <div class="w-full h-48 bg-gray-100 flex items-center justify-center rounded-lg">
                                <i class="fas fa-building text-4xl text-gray-400"></i>
                            </div>
                            @endif

                            {{-- BAGIAN BARU: Media Sosial & Website --}}
                            @if ($user->website || $user->linkedin || $user->instagram || $user->facebook || $user->youtube_link)
                            <div class="pt-6 border-t">
                                <h4 class="font-semibold text-gray-800 mb-4">Social Media & Links</h4>
                                <div class="flex flex-wrap gap-4">
                                    @if($user->website)
                                    <a href="{{ $user->website }}" target="_blank" class="text-gray-500 hover:text-blue-600" title="Website"><i class="fas fa-globe fa-2x"></i></a>
                                    @endif
                                    @if($user->linkedin)
                                    <a href="{{ $user->linkedin }}" target="_blank" class="text-gray-500 hover:text-blue-600" title="LinkedIn"><i class="fab fa-linkedin fa-2x"></i></a>
                                    @endif
                                    @if($user->instagram)
                                    <a href="{{ $user->instagram }}" target="_blank" class="text-gray-500 hover:text-blue-600" title="Instagram"><i class="fab fa-instagram fa-2x"></i></a>
                                    @endif
                                    @if($user->facebook)
                                    <a href="{{ $user->facebook }}" target="_blank" class="text-gray-500 hover:text-blue-600" title="Facebook"><i class="fab fa-facebook fa-2x"></i></a>
                                    @endif
                                    @if($user->youtube_link)
                                    <a href="{{ $user->youtube_link }}" target="_blank" class="text-gray-500 hover:text-blue-600" title="YouTube"><i class="fab fa-youtube fa-2x"></i></a>
                                    @endif
                                </div>
                            </div>
                            @endif
                        </div>

                        {{-- Kolom Kanan: Detail Informasi --}}
                        <div class="md:col-span-2">
                            <p class="text-sm font-semibold text-blue-600">{{ $user->tipe_instansi }}</p>
                            <h1 class="text-3xl font-bold text-gray-900 mt-1">{{ $user->nama_instansi }}</h1>
                            <h1 class="text-3xl font-bold text-greener mt-1">No. Booth: {{ $user->booth_number }}</h1>

                            @if($user->description)
                            <p class="mt-4 text-gray-700 text-base leading-relaxed">
                                {{ $user->description }}
                            </p>
                            @endif

                            <div class="mt-6 pt-6 border-t">
                                <h4 class="font-semibold text-gray-800 mb-4">Contact Information</h4>
                                <div class="space-y-3 text-gray-600">
                                    <p><strong class="w-32 inline-block">Contact Person</strong>: {{ $user->name }} ({{ $user->jabatan }})</p>
                                    @if($user->phone_instansi)
                                    <p><strong class="w-32 inline-block">Telepon Instansi</strong>: {{ $user->phone_instansi }}</p>
                                    @endif
                                    @if($user->whatsapp)
                                    <p><strong class="w-32 inline-block">WhatsApp</strong>: <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $user->whatsapp) }}" target="_blank" class="text-green-600 hover:underline">{{ $user->whatsapp }} <i class="fab fa-whatsapp ml-1"></i></a></p>
                                    @endif
                                    <p><strong class="w-32 inline-block">Email</strong>: <a href="mailto:{{ $user->email }}" class="text-blue-600 hover:underline">{{ $user->email }}</a></p>
                                    @if($user->alamat)
                                    <p><strong class="w-32 inline-block">Alamat</strong>: {{ $user->alamat }}</p>
                                    @endif
                                </div>
                            </div>

                            {{-- BAGIAN BARU: Materi & Unduhan --}}
                            @if($user->document_path || $user->document_link)
                            <div class="mt-6 pt-6 border-t">
                                <h4 class="font-semibold text-gray-800 mb-4">Materials & Downloads</h4>
                                <div class="flex flex-wrap gap-4">
                                    @if($user->document_path)
                                    <a href="{{ asset('storage/' . $user->document_path) }}" download class="px-5 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 text-sm">
                                        <i class="fas fa-download mr-2"></i> Download Brosur
                                    </a>
                                    @endif
                                    @if($user->document_link)
                                    <a href="{{ $user->document_link }}" target="_blank" class="px-5 py-2 bg-gray-700 text-white font-semibold rounded-lg hover:bg-gray-800 text-sm">
                                        <i class="fas fa-external-link-alt mr-2"></i> Lihat Materi Eksternal
                                    </a>
                                    @endif
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>