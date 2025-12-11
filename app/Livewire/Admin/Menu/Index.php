<?php

namespace App\Livewire\Admin\Menu;

use App\Models\MenuItem;
use Livewire\Component;
use Livewire\Attributes\Layout;

class Index extends Component
{
    public $menuItems;
    public $parentOptions;

    // Properti untuk modal form
    public bool $showModal = false;
    public bool $isEditMode = false;
    public $menuItemId;
    public $label_en, $label_id, $link, $parent_id, $target;

    public $location;
    public bool $showLocationSelector = false;

    protected function rules()
    {
        return [
            'label_en' => 'required|string|max:255',
            'label_id' => 'required|string|max:255',
            'link' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:menu_items,id',
            'target' => 'required|in:_self,_blank',
            'location' => 'nullable|string|in:header,footer_nav,footer_legal',
        ];
    }

    public function mount()
    {
        $this->loadMenuItems();
    }

    public function loadMenuItems()
    {
        $this->menuItems = MenuItem::whereNull('parent_id')
            ->with('children') // Eager load children
            ->orderBy('order')
            ->get();

        // Siapkan data untuk dropdown parent
        $this->parentOptions = MenuItem::whereNull('parent_id')->orderBy('label')->pluck('label', 'id');
    }

    private function resetForm()
    {
        $this->isEditMode = false;
        $this->menuItemId = null;
        $this->label_en = '';
        $this->label_id = '';
        $this->link = '#';
        $this->parent_id = null;
        $this->target = '_self';
        $this->location = null;
        $this->showLocationSelector = false;
    }

    public function create()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit($id)
    {
        $item = MenuItem::findOrFail($id);
        $this->menuItemId = $item->id;
        $this->label_en = $item->getTranslation('label', 'en');
        $this->label_id = $item->getTranslation('label', 'id');
        $this->link = $item->link;
        $this->parent_id = $item->parent_id;
        $this->target = $item->target;

        $this->location = $item->location;
        $this->showLocationSelector = !empty($item->location);

        $this->isEditMode = true;
        $this->showModal = true;
    }

    public function save()
    {
        $validatedData = $this->validate();

        $data = [
            'label' => [
                'en' => $validatedData['label_en'],
                'id' => $validatedData['label_id'],
            ],
            'link' => $validatedData['link'],
            'parent_id' => $validatedData['parent_id'],
            'target' => $validatedData['target'],
            'location' => $this->showLocationSelector ? $this->location : null,
        ];

        if ($this->isEditMode) {
            MenuItem::findOrFail($this->menuItemId)->update($data);
            session()->flash('message', 'Menu item updated successfully.');
        } else {
            MenuItem::create($data);
            session()->flash('message', 'Menu item created successfully.');
        }

        $this->closeModal();
    }


    public function delete($id)
    {
        MenuItem::findOrFail($id)->delete();
        session()->flash('message', 'Menu item deleted successfully.');
        $this->loadMenuItems();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function updateMenuOrder($items)
    {
        $this->updateOrderRecursive($items);
        session()->flash('message', 'Menu order updated successfully.');
    }
    protected function updateOrderRecursive($menuItems, $parentId = null)
    {
        foreach ($menuItems as $index => $item) {

            // Konversi ID dari string (jika dari JS) ke integer
            $itemId = (int) $item['value'];

            MenuItem::find($itemId)->update([
                'order' => $index + 1,        // Simpan urutan barunya (mulai dari 1)
                'parent_id' => $parentId      // Simpan parent_id barunya
            ]);

            // Jika item ini memiliki children, panggil fungsi ini lagi (rekursif)
            if (!empty($item['items'])) {
                $this->updateOrderRecursive($item['items'], $itemId);
            }
        }
    }

    public function render()
    {
        // Muat ulang menu items setiap kali ada perubahan
        $this->loadMenuItems();
        return view('livewire.admin.menu.index')
            ->layout('layouts.app');
    }
}
