<?php

namespace App\Livewire\Admin\Form;

use App\Models\InquiryForm;
use Livewire\Component;
use Illuminate\Support\Str;

class Index extends Component
{
    public $form_id;
    public $name, $slug;
    public array $fields = [];

    public $showModal = false;
    public $isEditMode = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'fields.*.label' => 'required|string',
        'fields.*.type' => 'required|string',
    ];

    public function addField()
    {
        $this->fields[] = ['label' => '', 'type' => 'text', 'required' => false, 'options' => ''];
    }

    public function removeField($index)
    {
        // Hapus field dari array
        unset($this->fields[$index]);
        $this->fields = array_values($this->fields);
    }

    public function render()
    {
        $forms = InquiryForm::latest()->get();
        return view('livewire.admin.form.index', ['forms' => $forms])->layout('layouts.app');
    }

    public function create()
    {
        $this->resetForm();
        $this->isEditMode = false;
        $this->showModal = true;
    }

    public function edit($id)
    {
        $form = InquiryForm::findOrFail($id);
        $this->form_id = $form->id;
        $this->name = $form->name;
        $this->slug = $form->slug;

        $this->fields = collect($form->fields ?? [])->map(function ($field) {
            $field['options'] = implode(', ', $field['options'] ?? []);
            return $field;
        })->toArray();

        $this->isEditMode = true;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        /// Buat nama field dari label secara otomatis
        $sanitizedFields = collect($this->fields)
            // BARIS BARU: Abaikan field jika labelnya kosong
            ->filter(fn($field) => !empty(trim($field['label'])))
            ->map(function ($field) {
                $field['name'] = Str::snake(trim($field['label']));
                if (!empty($field['options'])) {
                    $field['options'] = array_map('trim', explode(',', $field['options']));
                } else {
                    $field['options'] = [];
                }
                return $field;
            })->values()->toArray();

        $data = [
            'name' => $this->name,
            'slug' => Str::slug($this->name),
            'fields' => $sanitizedFields,
        ];

        if ($this->isEditMode) {
            InquiryForm::findOrFail($this->form_id)->update($data);
        } else {
            InquiryForm::create($data);
        }

        $this->closeModal();
        session()->flash('message', 'Form successfully saved.');
    }

    public function delete($id)
    {
        InquiryForm::findOrFail($id)->delete();
        session()->flash('message', 'Form successfully deleted.');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->form_id = null;
        $this->name = '';
        $this->slug = '';
        $this->fields = [];
    }
}
