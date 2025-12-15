<?php

namespace App\Livewire\Admin\FileManager;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;

class Index extends Component
{
    use WithFileUploads, WithPagination;

    // State Navigasi
    public $disk = 'google';
    public $currentPath = '/'; // Path saat ini (kosong = root)
    public $breadcrumbs = [];

    // State Upload & Input
    public $newUpload; // File yang sedang diupload
    public $newFolderName = ''; // Nama folder baru
    public $isCreatingFolder = false;

    // State UI
    public $viewMode = 'grid'; // 'grid' atau 'list'
    
    public $previewFile = null;
    
    public $pathToDelete = null;
    public $typeToDelete = null;
    
    public $diskQuota = [
        'used' => 0,
        'total' => 0,
        'percent' => 0,
        'used_formatted' => '0 B',
        'total_formatted' => '0 B'
    ];
    
    // State Search & Filter
    public $searchQuery = '';
    public $filterType = 'all';
    
    // --- Properti Mode Picker ---
    public $isPicker = false; // Default false (Mode Halaman Biasa)
    public $eventNameToEmit = 'fileSelected'; // Nama event default untuk dikirim balik
    

    public function mount()
    {
        $this->refresh();
        $this->fetchDiskQuota(); // <-- Panggil fungsi ini saat komponen dimuat
    }
    
    // Method Baru: Ambil Info Kuota
    public function fetchDiskQuota()
    {
        try {
            // Ambil konfigurasi dari config/filesystems.php
            $config = config('filesystems.disks.google');

            // Setup Client Manual
            $client = new \Google\Client();
            $client->setClientId($config['clientId']);
            $client->setClientSecret($config['clientSecret']);
            $client->refreshToken($config['refreshToken']);

            // Panggil API Drive Service
            $service = new \Google\Service\Drive($client);
            
            // Request field 'storageQuota'
            $about = $service->about->get(['fields' => 'storageQuota']);
            $quota = $about->storageQuota;

            // Hitung Data
            $limit = (float) $quota->limit; // Total kuota (bisa 0 jika unlimited)
            $usage = (float) $quota->usage; // Terpakai

            $percent = ($limit > 0) ? round(($usage / $limit) * 100) : 0;

            $this->diskQuota = [
                'used' => $usage,
                'total' => $limit,
                'percent' => $percent,
                'used_formatted' => $this->formatSize($usage),
                'total_formatted' => ($limit > 0) ? $this->formatSize($limit) : 'Unlimited'
            ];

        } catch (\Exception $e) {
            // Log error diam-diam agar tidak merusak tampilan utama
            \Illuminate\Support\Facades\Log::error('Gagal ambil kuota Drive: ' . $e->getMessage());
        }
    }
    
    // Method Trigger Preview
    public function triggerPreview($path, $mimeType)
    {
        // Izinkan image DAN pdf
        if (str_starts_with($mimeType, 'image/') || $mimeType === 'application/pdf') {
            $this->previewFile = [
                'path' => $path,
                'name' => basename($path),
                'mime_type' => $mimeType
            ];
        } else {
            return $this->downloadItem($path);
        }
    }

    // Method Tutup Preview
    public function closePreview()
    {
        $this->previewFile = null;
    }
    
    
    public function selectFile($path)
    {
        // 1. Ambil URL Publik (Streaming/Temporary URL)
        // Kita gunakan URL streaming lokal kita agar preview cepat muncul
        // Atau bisa juga URL asli Google Drive jika ingin disimpan link-nya
        
        // Opsi A: Kirim Path Google Drive (Recommended untuk Spatie Media Library)
        // Kita kirim path-nya, nanti Controller yang akan download & proses.
        
        $data = [
            'path' => $path,
            'driver' => $this->disk, // 'google'
            'preview_url' => route('admin.files.stream', ['path' => $path]) // Untuk preview di form
        ];

        // 2. Kirim data ke komponen pemanggil (Form Berita/Event)
        $this->dispatch($this->eventNameToEmit, data: $data);

        // 3. Tutup Modal
        $this->dispatch('close-file-manager-modal');
    }

    // --- LOGIKA UTAMA ---

    private function fetchContent()
    {
        // Buat kunci cache unik berdasarkan Folder saat ini + Query Search + Filter
        // Agar kalau ganti folder, cache-nya beda.
        $cacheKey = 'gdrive_files_' . md5($this->currentPath . $this->searchQuery . $this->filterType);

        // Simpan data di cache selama 10 menit (600 detik)
        // Jika data ada di cache, ambil dari situ. Jika tidak, baru tanya Google.
        return Cache::remember($cacheKey, 600, function () {
            
            $allFiles = [];
            $allDirs = [];

            try {
                if (!empty($this->searchQuery)) {
                    // Search Mode (Hati-hati, ini berat karena scan satu Google Drive)
                    $contents = Storage::disk($this->disk)->listContents('/', true);
                } else {
                    // Normal Mode
                    $contents = Storage::disk($this->disk)->listContents($this->currentPath);
                }

                foreach ($contents as $item) {
                    $name = basename($item['path']);

                    // Filter Nama (Search)
                    if (!empty($this->searchQuery) && stripos($name, $this->searchQuery) === false) {
                        continue;
                    }

                    if ($item['type'] === 'dir') {
                        $allDirs[] = [
                            'name' => $name,
                            'path' => $item['path'],
                            'last_modified' => $item['last_modified'] ?? 0,
                        ];
                    } else {
                        // --- Logika Deteksi Tipe (Sama seperti sebelumnya) ---
                        $mime = $item['mime_type'] ?? $item['mimetype'] ?? 'unknown';
                        $extension = strtolower(pathinfo($item['path'], PATHINFO_EXTENSION));
                        $ambiguousMimes = ['application/octet-stream', 'unknown', 'application/x-troff-man'];

                        if (in_array($mime, $ambiguousMimes) || in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'pdf'])) {
                            $mime = match($extension) {
                                'jpg', 'jpeg' => 'image/jpeg',
                                'png' => 'image/png',
                                'gif' => 'image/gif',
                                'webp' => 'image/webp',
                                'pdf' => 'application/pdf',
                                default => $mime
                            };
                        }

                        // Filter Tab (Image/Document)
                        if ($this->filterType === 'image' && !str_starts_with($mime, 'image/')) continue;
                        if ($this->filterType === 'document' && str_starts_with($mime, 'image/')) continue;

                        $allFiles[] = [
                            'name' => $name,
                            'path' => $item['path'],
                            'size' => $this->formatSize($item['file_size'] ?? 0),
                            'mime_type' => $mime,
                            'last_modified' => $item['last_modified'] ?? 0,
                        ];
                    }
                }
            } catch (\Exception $e) {
                // Silent fail or log
            }

            // Urutkan Folder & File agar rapi (Folder duluan)
            usort($allDirs, fn($a, $b) => strnatcasecmp($a['name'], $b['name']));
            usort($allFiles, fn($a, $b) => strnatcasecmp($a['name'], $b['name']));

            return ['dirs' => $allDirs, 'files' => $allFiles];
        });
    }
    
    public function clearCache()
    {
        // Hapus semua kemungkinan cache untuk path ini
        // Karena kita tidak tahu persis key search/filternya, kita bisa pakai tag (jika driver mendukung)
        // Atau cara brutal: Cache::flush(); (Hati-hati menghapus semua cache aplikasi)
        
        // Cara Aman: Karena key kita pakai md5, agak susah ditebak.
        // Trik: Kita cukup ubah logika refresh() untuk memaksa Cache::forget
        
        // Versi Simpel: Kita panggil ini di method upload/delete
        Cache::forget('gdrive_files_' . md5($this->currentPath . $this->searchQuery . $this->filterType));
        
        // Jika sedang mode search, kita tidak tahu path mana yg berubah,
        // jadi sebaiknya biarkan user refresh manual atau tunggu expire.
    }
    
    public function updatedSearchQuery()
    {
        $this->resetPage(); // Reset ke halaman 1 saat search
    }
    
    public function updatedFilterType()
    {
        $this->resetPage(); // Reset ke halaman 1 saat ganti filter
    }
    
    public function refresh()
    {
        $this->generateBreadcrumbs();
    }

    public function navigate($path)
    {
        $this->searchQuery = '';
        $this->currentPath = $path;
        $this->resetPage(); // Reset pagination saat pindah folder
        $this->refresh();
    }

    public function navigateUp()
    {
        if ($this->currentPath == '/' || empty($this->currentPath)) return;
        $parts = explode('/', $this->currentPath);
        array_pop($parts);
        $this->currentPath = implode('/', $parts);
        $this->resetPage();
        $this->refresh();
    }

    private function generateBreadcrumbs()
    {
        $this->breadcrumbs = [];
        if (empty($this->currentPath) || $this->currentPath == '/') return;

        $parts = explode('/', $this->currentPath);
        $buildPath = '';
        
        foreach ($parts as $part) {
            if(empty($part)) continue;
            $buildPath .= ($buildPath == '' ? '' : '/') . $part;
            $this->breadcrumbs[] = [
                'name' => $part,
                'path' => $buildPath
            ];
        }
    }

    // --- AKSI USER ---

    public function createFolder()
    {
        $this->validate(['newFolderName' => 'required|string|max:100']);

        $path = $this->currentPath . '/' . $this->newFolderName;
        if($this->currentPath == '/') $path = $this->newFolderName; // Fix double slash di root

        Storage::disk($this->disk)->makeDirectory($path);
        
        $this->clearCache();

        $this->newFolderName = '';
        $this->isCreatingFolder = false;
        $this->refresh();
        $this->dispatch('swal:success', message: 'Folder berhasil dibuat');
    }

    public function updatedNewUpload()
    {
        // 1. Buat Validator Manual
        // Tujuannya agar kita bisa menangkap errornya dan kirim ke SweetAlert
        $validator = Validator::make(
            ['newUpload' => $this->newUpload],
            [
                'newUpload' => [
                    'required',
                    'file',
                    'max:51200', // 50MB
                    // Tambahkan 'svg' di sini jika Anda ingin MENGIZINKANNYA. 
                    // Jika ingin tetap melarang, biarkan list ini tanpa svg.
                    'mimes:jpg,jpeg,png,webp,pdf,doc,docx,xls,xlsx,ppt,pptx,zip,rar,svg', 
                ],
            ],
            [
                // Custom Messages (Opsional, agar bahasa lebih enak)
                'newUpload.max' => 'Ukuran file terlalu besar (Max 50MB).',
                'newUpload.mimes' => 'Format file tidak didukung.',
            ]
        );

        // 2. Cek apakah Validasi Gagal?
        if ($validator->fails()) {
            // Ambil pesan error pertama
            $errorMessage = $validator->errors()->first('newUpload');
            
            // Kirim ke SweetAlert
            $this->dispatch('swal:error', message: $errorMessage);
            
            // Reset input file agar user bisa coba lagi
            $this->newUpload = null;
            return; // Stop proses
        }

        // 3. Jika Lolos Validasi, Lanjut Upload
        try {
            $filename = $this->newUpload->getClientOriginalName();
            $path = $this->newUpload->storeAs($this->currentPath, $filename, $this->disk);

            if ($path) {
                $this->dispatch('swal:success', message: 'File berhasil diupload!');
                $this->clearCache();
            } else {
                throw new \Exception("Gagal menyimpan file ke penyimpanan cloud.");
            }

        } catch (\Exception $e) {
            $this->dispatch('swal:error', message: 'Gagal Upload: ' . $e->getMessage());
            \Illuminate\Support\Facades\Log::error('Google Drive Upload Error: ' . $e->getMessage());
        }

        $this->newUpload = null;
        $this->refresh();
    }

    // 1. Method Pemicu (Dipanggil saat tombol diklik)
    public function deleteItem($path, $type = 'file')
    {
        $this->pathToDelete = $path;
        $this->typeToDelete = $type;

        // Kirim event ke browser untuk menampilkan SweetAlert
        $this->dispatch('show-delete-confirmation');
    }

    // 2. Method Eksekusi (Dipanggil setelah user klik "Ya" di SweetAlert)
    #[On('perform-delete')] 
    public function destroy()
    {
        if (!$this->pathToDelete) return;

        try {
            if ($this->typeToDelete === 'dir') {
                Storage::disk($this->disk)->deleteDirectory($this->pathToDelete);
            } else {
                Storage::disk($this->disk)->delete($this->pathToDelete);
            }

            $this->clearCache();
            $this->dispatch('swal:success', message: 'Item berhasil dihapus permanen.');
        } catch (\Exception $e) {
            $this->dispatch('swal:error', message: 'Gagal menghapus: ' . $e->getMessage());
        }

        // Reset dan Refresh
        $this->pathToDelete = null;
        $this->typeToDelete = null;
        $this->refresh();
    }

    public function downloadItem($path)
    {
        return Storage::disk($this->disk)->download($path);
    }

    public function getShareLink($path)
    {
        // KITA GANTI LOGIKA INI:
        // $url = Storage::disk($this->disk)->url($path); 
        
        // MENJADI INI (Proxy Streamer):
        // URL ini akan terlihat seperti: https://website-anda.com/media/public/stream?path=Folder/File.jpg
        // URL ini AMAN dan CEPAT untuk dipakai di tag <img src="..."> di mana saja.
        $url = route('media.stream.public', ['path' => $path]);
        
        // Kirim URL ke browser untuk dicopy ke clipboard
        $this->dispatch('show-share-link', link: $url);
    }

    // --- HELPER ---

    private function formatSize($bytes)
    {
        if ($bytes >= 1073741824) return number_format($bytes / 1073741824, 2) . ' GB';
        if ($bytes >= 1048576) return number_format($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024) return number_format($bytes / 1024, 2) . ' KB';
        return $bytes . ' bytes';
    }
    
    

    public function render()
    {
        $data = $this->fetchContent();
        
        $directories = $data['dirs'];
        $rawFiles = $data['files'];

        // 3. Manual Pagination untuk Files
        $perPage = 20;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentFiles = array_slice($rawFiles, ($currentPage - 1) * $perPage, $perPage);
        
        $paginatedFiles = new LengthAwarePaginator(
            $currentFiles,
            count($rawFiles),
            $perPage,
            $currentPage,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );

        return view('livewire.admin.file-manager.index', [
            'directories' => $directories,
            'files' => $paginatedFiles // Kirim objek paginator ke view
        ])->layout('layouts.app');
    }
}