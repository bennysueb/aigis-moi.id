<?php

namespace App\Livewire\Admin\SocialWall;

use App\Models\SocialMediaType;
use App\Models\SocialWallItem;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

class Index extends Component
{
    use WithPagination;

    // Properti untuk form Social Media Type
    public ?SocialMediaType $editingType = null;
    public $newTypeName = '';
    public $newTypeIconClass = '';

    // Properti untuk form Social Wall Item
    public ?SocialWallItem $editingItem = null;
    public $newItemEmbedCode = '';
    public $newItemSocialMediaTypeId = '';

    // Properti untuk modal
    public $showTypeModal = false;
    public $showItemModal = false;

    protected function rules()
    {
        return [
            'newTypeName' => 'required|string|max:255|unique:social_media_types,name,' . ($this->editingType ? $this->editingType->id : ''),
            'newTypeIconClass' => 'required|string|max:255',
            'newItemEmbedCode' => 'required|string',
            'newItemSocialMediaTypeId' => 'required|exists:social_media_types,id',
        ];
    }

    public function openTypeModal()
    {
        $this->resetErrorBag();
        $this->editingType = null;
        $this->newTypeName = '';
        $this->newTypeIconClass = '';
        $this->showTypeModal = true;
    }

    public function editType(SocialMediaType $type)
    {
        $this->resetErrorBag();
        $this->editingType = $type;
        $this->newTypeName = $type->name;
        $this->newTypeIconClass = $type->icon_class;
        $this->showTypeModal = true;
    }

    public function saveType()
    {
        $this->validate([
            'newTypeName' => 'required|string|max:255|unique:social_media_types,name,' . ($this->editingType ? $this->editingType->id : ''),
            'newTypeIconClass' => 'required|string|max:255',
        ]);

        if ($this->editingType) {
            $this->editingType->update([
                'name' => $this->newTypeName,
                'icon_class' => $this->newTypeIconClass,
            ]);
            $this->dispatch('swal:toast', ['type' => 'success', 'message' => 'Social media type successfully updated.']);
        } else {
            SocialMediaType::create([
                'name' => $this->newTypeName,
                'icon_class' => $this->newTypeIconClass,
            ]);
            $this->dispatch('swal:toast', ['type' => 'success', 'message' => 'Social media type successfully added.']);
        }

        $this->showTypeModal = false;
    }

    public function confirmDeleteType($id)
    {
        // PERUBAHAN: Menggunakan named arguments, bukan array
        $this->dispatch(
            'swal:confirm',
            title: 'Are you sure?',
            text: 'This action cannot be undone!',
            id: $id,
            method: 'deleteTypeConfirmed',
            component: 'admin.social-wall.index'
        );
    }

    #[On('deleteTypeConfirmed')]
    public function deleteTypeConfirmed($id)
    {
        // Ini adalah kode dari langkah kita sebelumnya, sudah benar
        $type = SocialMediaType::find($id);
        if ($type) {
            $type->delete();
            $this->dispatch('swal:toast', ['type' => 'success', 'message' => 'Type successfully deleted.']);
        } else {
            $this->dispatch('swal:toast', ['type' => 'error', 'message' => 'Type not found or already deleted.']);
        }
        $this->resetPage();
    }


    public function openItemModal()
    {
        $this->resetErrorBag();
        $this->editingItem = null;
        $this->newItemEmbedCode = '';
        $this->newItemSocialMediaTypeId = '';
        $this->showItemModal = true;
    }

    public function editItem(SocialWallItem $item)
    {
        $this->resetErrorBag();
        $this->editingItem = $item;
        $this->newItemEmbedCode = $item->embed_code;
        $this->newItemSocialMediaTypeId = $item->social_media_type_id;
        $this->showItemModal = true;
    }

    public function saveItem()
    {
        $this->validate([
            'newItemEmbedCode' => 'required|string',
            'newItemSocialMediaTypeId' => 'required|exists:social_media_types,id',
        ]);

        $data = [
            'embed_code' => $this->newItemEmbedCode,
            'social_media_type_id' => $this->newItemSocialMediaTypeId,
            'user_id' => auth()->id(),
        ];

        if ($this->editingItem) {
            $this->editingItem->update($data);
            $this->dispatch('swal:toast', ['type' => 'success', 'message' => 'Social wall item successfully updated.']);
        } else {
            SocialWallItem::create($data);
            $this->dispatch('swal:toast', ['type' => 'success', 'message' => 'Social wall item successfully added.']);
        }

        $this->showItemModal = false;
    }

    public function togglePublish(SocialWallItem $item)
    {
        $item->update(['is_published' => !$item->is_published]);
        $this->dispatch('swal:toast', ['type' => 'success', 'message' => 'Item status successfully updated.']);
    }

    public function confirmDeleteItem($id)
    {
        // PERUBAHAN: Menggunakan named arguments, bukan array
        $this->dispatch(
            'swal:confirm',
            title: 'Are you sure?',
            text: 'You won\'t be able to revert this!',
            id: $id,
            method: 'deleteItemConfirmed',
            component: 'admin.social-wall.index'
        );
    }

    #[On('deleteItemConfirmed')]
    public function deleteItemConfirmed($id)
    {
        // Ini adalah kode dari langkah kita sebelumnya, sudah benar
        $item = SocialWallItem::find($id);
        if ($item) {
            $item->delete();
            $this->dispatch('swal:toast', ['type' => 'success', 'message' => 'Item successfully deleted.']);
        } else {
            $this->dispatch('swal:toast', ['type' => 'error', 'message' => 'Item not found or already deleted.']);
        }
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.admin.social-wall.index', [
            'socialWallItems' => SocialWallItem::with('socialMediaType', 'user')->latest()->paginate(10),
            'socialMediaTypes' => SocialMediaType::all(),
        ])->layout('layouts.app');
    }
}
