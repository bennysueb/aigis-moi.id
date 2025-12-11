<?php

namespace App\Livewire\Admin\Page;

use App\Models\Event;
use App\Models\Post;
use App\Models\SectionItem;
use App\Models\WelcomeSection;
use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\SectionTemplate;
use App\Models\CustomSection;

class WelcomeBuilder extends Component
{
    use WithPagination;

    public $sections;

    // Modal state untuk "Manage Items" (Events/News)
    public bool $showItemModal = false;
    public ?WelcomeSection $managingSection = null;
    public string $search = '';
    public array $selectedItems = [];

    // Modal state untuk "Custom Section"
    public bool $showTemplateSelectModal = false;
    public bool $showContentFillModal = false;
    public $sectionTemplates;
    public ?SectionTemplate $selectedTemplate = null;

    // ============================================
    // PERUBAHAN 1: Struktur $content jadi nested
    // ============================================
    public array $content = ['en' => [], 'id' => []];

    public bool $isEditMode = false;
    public ?int $editingWelcomeSectionId = null;

    public bool $showDeleteModal = false;
    public ?int $deletingSectionId = null;

    // State untuk bahasa
    public string $currentLocale = 'en'; // Bahasa aktif di builder utama (tidak diubah)
    public array $supportedLocales = ['en', 'id'];

    // BARU: State untuk bahasa di dalam modal "Fill Content"
    public string $modalLocale = 'en';

    public function mount()
    {
        $this->loadSections();
    }

    public function loadSections()
    {
        $this->sections = WelcomeSection::with('customSection.template')
            ->orderBy('order')
            ->get();
    }

    // --- FUNGSI UNTUK REORDER, TOGGLE VISIBILITY ---
    // (Tidak ada perubahan di sini)
    public function updateOrder($orderedIds)
    {
        foreach ($orderedIds as $index => $id) {
            WelcomeSection::where('id', $id)->update(['order' => $index + 1]);
        }
        $this->loadSections();
        $this->dispatch('notify', 'Sections reordered successfully!');
    }

    public function toggleVisibility(WelcomeSection $section)
    {
        $section->update(['is_visible' => !$section->is_visible]);
        $this->loadSections();
        $this->dispatch('notify', 'Visibility updated successfully!');
    }
    // --- AKHIR FUNGSI REORDER, TOGGLE VISIBILITY ---


    // --- FUNGSI UNTUK MODAL "MANAGE ITEMS" (EVENTS/NEWS) ---
    // (Tidak ada perubahan di sini)
    public function manageItems(WelcomeSection $section)
    {
        $this->managingSection = $section->load('items.item');

        $this->selectedItems = $this->managingSection->items->map(function ($sectionItem) {

            // ======================= AWAL PERBAIKAN =======================
            // Cek dulu apakah item-nya (Event atau Post) masih ada
            if ($sectionItem->item === null) {
                // Jika tidak ada (sudah terhapus), kembalikan null
                return null;
            }
            // ======================= AKHIR PERBAIKAN ========================

            // Kode ini hanya akan berjalan jika $sectionItem->item TIDAK null
            return [
                'id' => $sectionItem->item->id,
                'title' => $sectionItem->item->getTranslation('name', app()->getLocale()) ?? $sectionItem->item->getTranslation('title', app()->getLocale()),
            ];
        })
            ->filter() // <-- TAMBAHKAN INI: Menghapus semua entri 'null' dari koleksi
            ->values() // <-- TAMBAHAN (Opsional tapi disarankan): Mengatur ulang key array
            ->toArray();

        $this->showItemModal = true;
    }

    public function closeModal()
    {
        $this->showItemModal = false;
        $this->managingSection = null;
        $this->selectedItems = [];
        $this->search = '';
        $this->resetPage();
    }

    public function addItem($itemId)
    {
        if (collect($this->selectedItems)->pluck('id')->contains($itemId)) return;
        $modelClass = $this->managingSection->component === 'events' ? Event::class : Post::class;
        $item = $modelClass::find($itemId);
        $this->selectedItems[] = [
            'id' => $item->id,
            'title' => $item->getTranslation('name', app()->getLocale()) ?? $item->getTranslation('title', app()->getLocale()),
        ];
    }

    public function removeItem($itemId)
    {
        $this->selectedItems = array_filter($this->selectedItems, fn($item) => $item['id'] != $itemId);
    }

    public function updateSelectedOrder($orderedIds)
    {
        $newOrder = [];
        foreach ($orderedIds as $id) {
            $found = collect($this->selectedItems)->firstWhere('id', $id);
            if ($found) $newOrder[] = $found;
        }
        $this->selectedItems = $newOrder;
    }

    public function saveItems()
    {
        SectionItem::where('welcome_section_id', $this->managingSection->id)->delete();
        $modelClass = $this->managingSection->component === 'events' ? Event::class : Post::class;
        foreach ($this->selectedItems as $index => $item) {
            SectionItem::create([
                'welcome_section_id' => $this->managingSection->id,
                'item_id' => $item['id'],
                'item_type' => $modelClass,
                'order' => $index + 1,
            ]);
        }
        $this->dispatch('notify', 'Content updated successfully!');
        $this->closeModal();
    }

    public function getAvailableItemsProperty()
    {
        if (!$this->managingSection) return new \Illuminate\Pagination\LengthAwarePaginator([], 0, 5);
        $query = null;
        $orderByColumn = 'created_at';
        $nameColumn = 'name';
        if ($this->managingSection->component === 'events') {
            $query = Event::query();
            $orderByColumn = 'start_date';
            $nameColumn = 'name';
        } elseif ($this->managingSection->component === 'news') {
            $query = Post::query()->where('published_at', '<=', now())->whereNotNull('published_at');
            $orderByColumn = 'published_at';
            $nameColumn = 'title';
        }
        if ($query) {
            return $query->when($this->search, fn($q) => $q->where($nameColumn . '->' . app()->getLocale(), 'like', '%' . $this->search . '%'))
                ->orderBy($orderByColumn, 'desc')->paginate(5);
        }
        return new \Illuminate\Pagination\LengthAwarePaginator([], 0, 5);
    }
    // --- AKHIR FUNGSI MODAL "MANAGE ITEMS" ---


    // --- FUNGSI UNTUK MODAL "CUSTOM SECTION" ---

    // BARU: Fungsi untuk berganti tab di modal "Fill Content"
    public function setModalLocale($locale)
    {
        if (in_array($locale, $this->supportedLocales)) {
            $this->modalLocale = $locale;
        }
    }

    public function openAddCustomSectionModal()
    {
        $this->isEditMode = false; // Pastikan mode create
        $this->resetCustomSectionForm(); // Reset form dulu
        $this->sectionTemplates = SectionTemplate::all();
        $this->showTemplateSelectModal = true;
    }

    // ============================================
    // PERUBAHAN 2: Inisialisasi $content jadi nested
    // ============================================
    public function selectTemplate($templateId)
    {
        $this->selectedTemplate = SectionTemplate::find($templateId);
        $this->content = []; // Reset $content

        // Inisialisasi struktur nested berdasarkan field template
        if ($this->selectedTemplate && isset($this->selectedTemplate->fields)) {
            foreach ($this->supportedLocales as $locale) {
                $this->content[$locale] = []; // Buat sub-array per bahasa
                foreach ($this->selectedTemplate->fields as $field) {
                    $this->content[$locale][$field['name']] = ''; // Inisialisasi field kosong
                }
            }
        }

        $this->modalLocale = 'en'; // Mulai modal dari tab English
        $this->showTemplateSelectModal = false;
        $this->showContentFillModal = true;
    }

    // ============================================
    // PERUBAHAN 3: Logika Edit jadi multi-bahasa
    // ============================================
    public function editCustomSection($welcomeSectionId)
    {
        $this->isEditMode = true;
        $this->editingWelcomeSectionId = $welcomeSectionId;

        $welcomeSection = WelcomeSection::with('customSection.template')->find($welcomeSectionId);

        if ($welcomeSection && $welcomeSection->customSection) {
            $this->selectedTemplate = $welcomeSection->customSection->template;
            // Ambil data content (sudah dalam format JSON/array)
            $existingContent = $welcomeSection->customSection->content;

            // Isi properti $content, pastikan semua locale & field ada
            foreach ($this->supportedLocales as $locale) {
                $this->content[$locale] = $existingContent[$locale] ?? []; // Ambil data locale yg ada
                if ($this->selectedTemplate) {
                    foreach ($this->selectedTemplate->fields as $field) {
                        // Jika field tidak ada di data lama, default ke string kosong
                        if (!isset($this->content[$locale][$field['name']])) {
                            $this->content[$locale][$field['name']] = '';
                        }
                    }
                }
            }

            $this->modalLocale = 'en'; // Selalu mulai modal dari tab English
            $this->showContentFillModal = true;
        } else {
            // Handle jika data tidak ditemukan (misalnya, tampilkan error)
            $this->dispatch('swal:error', ['title' => 'Error!', 'text' => 'Section data not found.']);
            $this->closeCustomSectionModals();
        }
    }

    // ============================================
    // PERUBAHAN 4: Validasi & Save jadi multi-bahasa
    // ============================================
    public function saveCustomSection()
    {
        // Validasi SEMUA field di SEMUA bahasa
        $rules = [];
        if ($this->selectedTemplate && isset($this->selectedTemplate->fields)) {
            foreach ($this->supportedLocales as $locale) {
                foreach ($this->selectedTemplate->fields as $field) {
                    // Rule validasi per-locale per-field
                    // Sesuaikan rule jika perlu (misal: 'nullable' untuk bahasa sekunder)
                    $rules["content.{$locale}.{$field['name']}"] = 'required';
                }
            }
        }
        // Validasi juga nama template (meskipun tidak di-bind langsung, perlu ada)
        if (!$this->isEditMode) {
            $rules['selectedTemplate'] = 'required';
        }

        $this->validate($rules);

        if ($this->isEditMode) {
            // LOGIKA UNTUK UPDATE
            $welcomeSection = WelcomeSection::find($this->editingWelcomeSectionId);
            if ($welcomeSection && $welcomeSection->customSection) {
                $welcomeSection->customSection->update([
                    'content' => $this->content, // Simpan array nested $content
                ]);
                // Opsional: Update nama WelcomeSection jika nama template berubah
                // $welcomeSection->update(['name' => $this->selectedTemplate->getTranslations('name')]);
            }
            $message = 'Custom section updated successfully!';
        } else {
            // LOGIKA UNTUK CREATE
            $customSection = CustomSection::create([
                'section_template_id' => $this->selectedTemplate->id,
                'content' => $this->content, // Simpan array nested $content
            ]);

            WelcomeSection::create([
                'name' => $this->selectedTemplate->getTranslations('name'),
                'custom_section_id' => $customSection->id,
                'is_visible' => true,
                'order' => (WelcomeSection::max('order') ?? 0) + 1,
            ]);
            $message = 'New custom section added successfully!';
        }

        $this->closeCustomSectionModals();
        $this->loadSections();
        $this->dispatch('swal:success', ['title' => 'Success!', 'text' => $message]);
    }

    // ============================================
    // PERUBAHAN 5: Reset $content jadi nested
    // ============================================
    private function resetCustomSectionForm()
    {
        $this->selectedTemplate = null;
        $this->content = ['en' => [], 'id' => []]; // Reset ke struktur nested kosong
        $this->modalLocale = 'en'; // Reset tab modal ke English
        $this->editingWelcomeSectionId = null;
        $this->isEditMode = false;
        $this->resetValidation();
    }

    public function closeCustomSectionModals()
    {
        $this->showTemplateSelectModal = false;
        $this->showContentFillModal = false;
        $this->resetCustomSectionForm(); // Panggil fungsi reset yang baru
    }
    // --- AKHIR FUNGSI MODAL "CUSTOM SECTION" ---


    // --- FUNGSI UNTUK DELETE CUSTOM SECTION ---
    // (Tidak ada perubahan di sini)
    public function confirmDelete($sectionId)
    {
        $this->deletingSectionId = $sectionId;
        $this->showDeleteModal = true;
    }

    public function deleteCustomSection()
    {
        $welcomeSection = WelcomeSection::find($this->deletingSectionId);
        if ($welcomeSection && $welcomeSection->custom_section_id) {
            // Hapus CustomSection dulu, baru WelcomeSection
            CustomSection::find($welcomeSection->custom_section_id)->delete();
            $welcomeSection->delete();
            $this->loadSections();
            $this->dispatch('swal:success', [
                'title' => 'Section Deleted!',
                'text' => 'The custom section has been successfully removed.',
            ]);
        } else {
            $this->dispatch('swal:error', [
                'title' => 'Error!',
                'text' => 'Could not delete section.',
            ]);
        }
        $this->showDeleteModal = false;
        $this->deletingSectionId = null;
    }
    // --- AKHIR FUNGSI DELETE ---

    public function render()
    {
        return view('livewire.admin.page.welcome-builder', [
            'availableItems' => $this->availableItems
        ])->layout('layouts.app');
    }
}
