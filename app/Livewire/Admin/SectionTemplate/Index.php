<?php

namespace App\Livewire\Admin\SectionTemplate;

use App\Models\SectionTemplate;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class Index extends Component
{
    use WithPagination;

    // Properti untuk state & UI
    public string $search = '';
    public bool $showModal = false;
    public bool $isEditMode = false;

    // Properti untuk data model SectionTemplate
    public $templateId;
    public $name;
    public $slug;
    public $html_content;
    public $css_content;
    public $fields = []; // Akan menampung array dari fields

    // Listener untuk auto-generate slug
    public function updatedName($value)
    {
        if (!$this->isEditMode) {
            $this->slug = Str::slug($value);
        }
    }

    public function render()
    {
        $templates = SectionTemplate::query()
            ->when($this->search, fn($query) => $query->where('name', 'like', '%' . $this->search . '%'))
            ->paginate(10);

        return view('livewire.admin.section-template.index', [
            'templates' => $templates,
        ])->layout('layouts.app');
    }

    // Metode untuk mengelola field dinamis
    public function addField()
    {
        $this->fields[] = ['name' => '', 'type' => 'text', 'label' => ''];
    }

    public function removeField($index)
    {
        unset($this->fields[$index]);
        $this->fields = array_values($this->fields); // Re-index array
    }

    // Metode untuk operasi CRUD
    public function create()
    {
        $this->isEditMode = false;
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit($id)
    {
        $template = SectionTemplate::findOrFail($id);

        $this->templateId = $template->id;
        $this->name = $template->name;
        $this->slug = $template->slug;
        $this->html_content = $template->html_content;
        $this->css_content = $template->css_content;
        $this->fields = $template->fields; // Otomatis di-decode dari JSON ke array

        $this->isEditMode = true;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'slug' => [
                'required',
                'string',
                Rule::unique('section_templates')->ignore($this->templateId),
            ],
            'html_content' => 'required|string',
            'css_content' => 'nullable|string',
            'fields' => 'present|array',
            'fields.*.label' => 'required|string',
            'fields.*.name' => 'required|string',
            'fields.*.type' => 'required|string',
        ]);

        $data = [
            'name' => $this->name,
            'slug' => $this->slug,
            'html_content' => $this->html_content,
            'css_content' => $this->css_content,
            'fields' => $this->fields,
        ];

        if ($this->isEditMode) {
            $template = SectionTemplate::findOrFail($this->templateId);
            $template->update($data);
            session()->flash('message', 'Template updated successfully.');
        } else {
            SectionTemplate::create($data);
            session()->flash('message', 'Template created successfully.');
        }

        $this->closeModal();
    }

    public function delete($id)
    {
        SectionTemplate::findOrFail($id)->delete();
        session()->flash('message', 'Template deleted successfully.');
    }

    private function resetForm()
    {
        $this->templateId = null;
        $this->name = '';
        $this->slug = '';
        $this->html_content = '';
        $this->css_content = '';
        $this->fields = [];
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }
}
