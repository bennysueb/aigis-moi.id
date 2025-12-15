<?php

namespace App\Livewire\Admin\Page;

use App\Models\Page;
use App\Models\SectionTemplate;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;

class PageBuilder extends Component
{
    public $pageId;

    public function getPageProperty()
    {
        return Page::find($this->pageId);
    }

    public $availableTemplates;

    // MODIFIKASI: $pageBlocks sekarang akan menampung array untuk 'en' dan 'id'
    public array $pageBlocks = ['en' => [], 'id' => []];

    public array $title = ['en' => '', 'id' => ''];
    public string $slug = '';

    public bool $showEditModal = false;
    public ?string $editingBlockId = null;

    public string $status = 'draft';

    public array $formData = [];
    public ?SectionTemplate $editingBlockTemplate = null;

    // BARU: Properti untuk mengelola bahasa
    public string $currentLocale = 'en';
    public array $supportedLocales = ['en', 'id'];

    public function mount(Page $page)
    {
        $this->pageId = $page->id;
        $this->availableTemplates = SectionTemplate::query()->orderBy('name')->get();

        // MODIFIKASI: Ambil SEMUA terjemahan untuk 'content', sama seperti Anda mengambil 'title'
        $this->pageBlocks = $page->getTranslations('content');

        // BARU: Pastikan kedua locale ada sebagai array (penting untuk UI nanti)
        foreach ($this->supportedLocales as $locale) {
            if (empty($this->pageBlocks[$locale]) || !is_array($this->pageBlocks[$locale])) {
                $this->pageBlocks[$locale] = [];
            }
        }

        $this->title = $page->getTranslations('title'); // Ini sudah benar
        $this->slug = $page->slug;
        $this->status = $page->status;
    }

    // BARU: Fungsi untuk berganti tab bahasa di UI
    public function setLocale($locale)
    {
        if (in_array($locale, $this->supportedLocales)) {
            $this->currentLocale = $locale;
        }
    }

    public function updatedTitleEn($value)
    {
        $this->slug = Str::slug($value);
    }

    public function save()
    {
        $validatedData = $this->validate([
            'title.en' => 'required|string|max:255',
            'title.id' => 'required|string|max:255',
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('pages', 'slug')->ignore($this->pageId),
            ],
            'status' => 'required|in:draft,published',
        ]);

        $pageToSave = $this->page;
        $pageToSave->setTranslation('title', 'en', $validatedData['title']['en']);
        $pageToSave->setTranslation('title', 'id', $validatedData['title']['id']);
        $pageToSave->slug = $validatedData['slug'];
        $pageToSave->status = $this->status;

        // MODIFIKASI: Gunakan setTranslations untuk 'content', sama seperti 'title'
        $pageToSave->setTranslations('content', $this->pageBlocks);

        $pageToSave->save();

        session()->flash('message', 'Page has been saved successfully!');
        return redirect()->route('admin.pages.index');
    }

    public function addBlock($templateSlug)
    {
        $template = SectionTemplate::where('slug', $templateSlug)->firstOrFail();
        $data = [];
        foreach ($template->fields as $field) {
            $data[$field['name']] = '';
        }

        // MODIFIKASI: Tambahkan blok ke locale yang sedang aktif
        $this->pageBlocks[$this->currentLocale][] = ['id' => uniqid('block_'), 'template_slug' => $template->slug, 'data' => $data];
    }

    public function removeBlock($blockId)
    {
        // MODIFIKASI: Hapus blok dari locale yang sedang aktif
        $this->pageBlocks[$this->currentLocale] = array_values(array_filter(
            $this->pageBlocks[$this->currentLocale],
            fn($block) => $block['id'] !== $blockId
        ));
    }

    public function editBlock($blockId)
    {
        // MODIFIKASI: Cari blok di locale yang sedang aktif
        $blockIndex = array_search($blockId, array_column($this->pageBlocks[$this->currentLocale], 'id'));

        if ($blockIndex === false) return;

        $block = $this->pageBlocks[$this->currentLocale][$blockIndex];
        $this->editingBlockId = $block['id'];

        $this->formData = $block['data'];
        $this->editingBlockTemplate = SectionTemplate::where('slug', $block['template_slug'])->first();
        $this->showEditModal = true;
    }

    public function updateBlock()
    {
        // MODIFIKASI: Update blok di locale yang sedang aktif
        $blockIndex = array_search($this->editingBlockId, array_column($this->pageBlocks[$this->currentLocale], 'id'));

        if ($blockIndex !== false) {
            $this->pageBlocks[$this->currentLocale][$blockIndex]['data'] = $this->formData;
        }
        $this->closeEditModal();
    }
    
    public function updateBlockOrder($orderedIds)
    {
        // 1. Ambil blok saat ini untuk locale yang aktif
        $currentBlocks = $this->pageBlocks[$this->currentLocale];
        $reorderedBlocks = [];

        // 2. Buat map array (ID => Block) agar pencarian cepat
        $blockMap = [];
        foreach ($currentBlocks as $block) {
            $blockMap[$block['id']] = $block;
        }

        // 3. Susun ulang array berdasarkan urutan ID yang dikirim dari frontend
        foreach ($orderedIds as $id) {
            if (isset($blockMap[$id])) {
                $reorderedBlocks[] = $blockMap[$id];
            }
        }

        // 4. Update properti utama
        $this->pageBlocks[$this->currentLocale] = $reorderedBlocks;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->editingBlockId = null;
        $this->formData = [];
        $this->editingBlockTemplate = null;
    }

    public function render()
    {
        return view('livewire.admin.page.page-builder')
            ->layout('layouts.app');
    }
}
