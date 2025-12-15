<?php

namespace App\Http\Controllers\Public; // <-- Perhatikan namespace 'Public'

use App\Http\Controllers\Controller;
use App\Models\Album; // <-- Import model Album kita

class GalleryController extends Controller
{

    public function index() // <-- METODE BARU
    {
        // 1. Ambil semua album.
        //    Kita gunakan 'with('media')' (eager loading)
        //    agar kita bisa menampilkan gambar cover dan jumlah foto
        //    tanpa membebani database (menghindari N+1 query).
        $albums = Album::with('media')
            ->latest() // Urutkan dari yang terbaru
            ->get();

        // 2. Kirim data semua album ke view 'index'
        return view('public.gallery.index', [
            'albums' => $albums
        ]);
    }
    /**
     * Menampilkan media di dalam album publik tertentu.
     *
     * @param  \App\Models\Album  $album
     * @return \Illuminate\View\View
     */
    public function showAlbum(Album $album)
    {
        // 1. Karena kita menggunakan Route-Model Binding (Album $album),
        //    Laravel sudah otomatis mengambil data album berdasarkan slug.

        // 2. Kita ambil semua media yang ada di dalam album ini
        //    menggunakan fungsi dari Spatie MediaLibrary.
        $mediaItems = $album->getMedia();

        // 3. Kita kirimkan data album dan koleksi media ke view.
        //    View ini akan kita buat di Langkah 3.
        return view('public.gallery.show-album', [
            'album' => $album,
            'mediaItems' => $mediaItems,
        ]);
    }
}
