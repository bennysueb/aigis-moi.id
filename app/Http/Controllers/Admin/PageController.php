<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PageController extends Controller
{
    public function create()
    {
        // Buat halaman draft kosong
        $page = Page::create([
            'title' => [
                'en' => 'Untitled Draft',
                'id' => 'Draf Tanpa Judul',
            ],
            'slug' => 'draft-' . Str::uuid(), // Slug unik sementara
            'content' => json_encode([]), // Konten JSON kosong
            'status' => 'draft',
        ]);

        // Langsung arahkan ke halaman builder untuk page yang baru dibuat
        return redirect()->route('admin.pages.builder', $page->id);
    }
}
