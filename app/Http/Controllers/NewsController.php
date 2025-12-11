<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;
use App\Models\Banner;


class NewsController extends Controller
{
    /**
     * ===================================================================
     * FUNGSI 'index' YANG DIPERBARUI
     * ===================================================================
     *
     * Menampilkan halaman daftar berita (utama atau berdasarkan kategori)
     */
    public function index(Request $request)
    {
        // --- 1. Ambil Featured Posts ---
        $featuredPosts = Post::where('published_at', '<=', now())
            ->whereJsonContains('visibility_options', 'featured')
            ->latest('published_at')
            ->get();

        // --- 2. Ambil Ads ---
        $ads = Banner::where('is_active', true)
            ->whereIn('position', ['event_ad_left', 'event_ad_right', 'event_ad_bottom'])
            ->with('media')
            ->get()
            ->keyBy('position');

        $leftAd = $ads->get('event_ad_left');
        $rightAd = $ads->get('event_ad_right');
        $bottomAd = $ads->get('event_ad_bottom');

        // --- 3. Ambil Breaking Posts ---
        $breakingPosts = Post::where('published_at', '<=', now())
            ->whereJsonContains('visibility_options', 'breaking')
            ->when($featuredPosts->isNotEmpty(), fn($q) => $q->whereNotIn('id', $featuredPosts->pluck('id')))
            ->latest('published_at')
            ->take(5)
            ->get();

        // --- 4. Ambil SEMUA Kategori untuk Sidebar (Hierarkis) ---
        $allCategories = Category::whereNull('parent_id')
            ->with(['children' => fn($q) => $q->withCount('posts')])
            ->withCount('posts')
            ->get();

        // Inisialisasi variabel
        $currentCategory = null;
        $filteredPosts = null;
        $categoriesWithPosts = collect();

        // ---- BARU: Inisialisasi $latestPosts ----
        $latestPosts = collect();

        if ($request->has('category')) {
            // --- 5. BLOK FILTER (jika user klik kategori) ---

            $categorySlug = $request->query('category');
            $currentCategory = Category::where('slug', $categorySlug)
                ->with('children') // Pastikan children di-load
                ->firstOrFail();

            // Kumpulkan ID dari induk DAN semua anaknya
            $categoryIds = $currentCategory->children->pluck('id')->push($currentCategory->id);

            // BARIS '->when(...)' SUDAH DIHAPUS DARI QUERY DI BAWAH INI
            $filteredPosts = Post::where('published_at', '<=', now())
                ->whereHas('categories', fn($q) => $q->whereIn('categories.id', $categoryIds))
                ->latest('published_at')
                ->paginate(12);
        } else {
            // --- 6. BLOK HALAMAN UTAMA (tidak difilter) ---

            // ---- BARU: Tambahkan kembali query $latestPosts ----
            $latestPosts = Post::where('published_at', '<=', now())
                ->when($featuredPosts->isNotEmpty(), fn($q) => $q->whereNotIn('id', $featuredPosts->pluck('id')))
                ->latest('published_at')->take(5)->get();

            $categoriesWithPosts = Category::whereNull('parent_id')
                ->where(function ($query) {
                    $query->whereHas('posts', fn($q) => $q->where('published_at', '<=', now()))
                        ->orWhereHas('children.posts', fn($q) => $q->where('published_at', '<=', now()));
                })
                ->with([
                    'posts' => fn($q) => $q
                        ->where('published_at', '<=', now())
                        ->latest(),
                    'children.posts' => fn($q) => $q
                        ->where('published_at', '<=', now())
                        ->latest()
                ])
                ->get();
        }

        // --- 7. Kembalikan View (Hanya satu return) ---
        return view('news.index', compact(
            'featuredPosts',
            'breakingPosts',
            'leftAd',
            'rightAd',
            'bottomAd',
            'allCategories',
            'currentCategory',
            'filteredPosts',
            'categoriesWithPosts',
            'latestPosts' // <-- BARU: Pastikan dikirim ke view
        ));
    }

    public function show(Post $post)
    {
        if (!$post->published_at || $post->published_at->isFuture()) {
            abort(404);
        }

        // --- 1. Ambil Ads (Kode Anda sudah benar) ---
        $ads = Banner::where('is_active', true)
            ->whereIn('position', ['event_ad_left', 'event_ad_right', 'event_ad_bottom'])
            ->with('media')
            ->get()
            ->keyBy('position');
        $leftAd = $ads->get('event_ad_left');
        $rightAd = $ads->get('event_ad_right');
        $bottomAd = $ads->get('event_ad_bottom');


        // --- 2. Ambil Featured Posts (Kode Anda sudah benar) ---
        $featuredPosts = Post::where('published_at', '<=', now())
            ->whereJsonContains('visibility_options', 'featured')
            ->where('id', '!=', $post->id)
            ->latest('published_at')
            ->get(); // <-- Gunakan get() bukan first()


        // --- 3. Ambil Recent Posts (Kode Anda sudah benar) ---
        $recentPosts = Post::where('published_at', '<=', now())
            ->where('id', '!=', $post->id)
            ->latest('published_at')
            ->take(5)
            ->get();

        // --- 4. Ambil Related Posts (Kode Anda sudah benar) ---
        $categoryIds = $post->categories->pluck('id');
        $relatedPosts = Post::where('published_at', '<=', now())
            ->where('id', '!=', $post->id)
            ->whereHas('categories', function ($query) use ($categoryIds) {
                $query->whereIn('categories.id', $categoryIds);
            })
            ->inRandomOrder()
            ->take(4)
            ->get();

        // --- 5. MODIFIKASI: Ambil Kategori untuk Sidebar (Hierarkis) ---
        // Query ini diubah agar hanya mengambil kategori induk (parent_id = null)
        // lalu memuat sub-kategori (children) beserta jumlah postingan untuk masing-masing.
        $categories = Category::whereNull('parent_id')
            ->with([
                'children' => fn($q) => $q->withCount(['posts' => fn($sq) => $sq->where('published_at', '<=', now())])
            ])
            ->withCount(['posts' => fn($q) => $q->where('published_at', '<=', now())])
            ->get(); // [cf. NewsController.php]

        // --- 6. Ambil Recommended Posts (Kode Anda sudah benar) ---
        $recommendedPosts = Post::where('published_at', '<=', now())
            ->whereJsonContains('visibility_options', 'recommended')
            ->where('id', '!=', $post->id)
            ->inRandomOrder()
            ->get();

        // --- 7. Kembalikan View (Kode Anda sudah benar) ---
        return view('news.show', compact(
            'post',
            'recentPosts',
            'relatedPosts',
            'categories', // Variabel ini sekarang berisi data hierarkis
            'recommendedPosts',
            'featuredPosts',
            'leftAd',
            'rightAd',
            'bottomAd'
        ));
    }
}
