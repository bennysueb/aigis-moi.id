<?php

namespace App\Livewire\Admin\Page;

use App\Models\Page;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $pages = Page::query()
            ->when($this->search, function ($query) {
                $searchTerm = '%' . strtolower($this->search) . '%';
                $query->whereRaw('LOWER(JSON_UNQUOTE(JSON_EXTRACT(title, "$.en"))) LIKE ?', [$searchTerm])
                    ->orWhereRaw('LOWER(JSON_UNQUOTE(JSON_EXTRACT(title, "$.id"))) LIKE ?', [$searchTerm]);
            })
            ->latest()
            ->paginate(15);

        return view('livewire.admin.page.index', ['pages' => $pages])
            ->layout('layouts.app');
    }

    /**
     * Buat halaman draft baru dan redirect ke Page Builder.
     */
    public function create()
    {
        // Buat halaman baru dengan judul sementara
        $page = Page::create([
            'title' => [
                'en' => 'Untitled Page',
                'id' => 'Halaman Tanpa Judul',
            ],
            'slug' => 'untitled-page-' . uniqid(), // Slug unik sementara
            'status' => 'draft',
            'content' => [], // Konten awal kosong
        ]);

        // Arahkan ke halaman builder untuk page yang baru dibuat
        return redirect()->route('admin.pages.builder', $page);
    }

    public function delete($id)
    {
        Page::findOrFail($id)->delete();
        session()->flash('message', 'Page successfully deleted.');
    }
}
