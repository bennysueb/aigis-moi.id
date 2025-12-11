<?php

namespace App\Livewire\Admin\FeedbackForm;

use App\Models\FeedbackForm;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    // Properti untuk UI
    public $showModal = false;
    public $isEditMode = false;
    public $search = '';

    // Properti untuk form
    public $formId;
    public $name;
    public array $fields = [];

    protected $rules = [
        'name' => 'required|string|max:255',
        'fields.*.label' => 'required|string',
        'fields.*.name' => 'required|string',
        'fields.*.type' => 'required|in:text,textarea,select,rating',
        'fields.*.required' => 'required|boolean',
        'fields.*.options' => 'nullable|string',
    ];

    public function mount()
    {
        $this->resetForm();
    }

    public function addField()
    {
        // Tambahkan 'options' => ''
        $this->fields[] = ['label' => '', 'name' => '', 'type' => 'text', 'required' => false, 'options' => ''];
    }

    public function removeField($index)
    {
        unset($this->fields[$index]);
        $this->fields = array_values($this->fields);
    }

    public function create()
    {
        $this->resetForm();
        $this->isEditMode = false;
        $this->showModal = true;
    }

    public function edit($id)
    {
        $form = FeedbackForm::findOrFail($id);
        $this->formId = $form->id;
        $this->name = $form->name;
        $this->fields = $form->fields ?? [];
        $this->isEditMode = true;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'fields' => $this->fields,
        ];

        if ($this->isEditMode) {
            FeedbackForm::findOrFail($this->formId)->update($data);
        } else {
            FeedbackForm::create($data);
        }

        $this->closeModal();
        session()->flash('message', 'Feedback form saved successfully.');
    }

    public function delete($id)
    {
        FeedbackForm::findOrFail($id)->delete();
        session()->flash('message', 'Feedback form deleted successfully.');
    }

    private function resetForm()
    {
        $this->formId = null;
        $this->name = '';
        $this->fields = [];
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function render()
    {
        $forms = FeedbackForm::where('name', 'like', '%' . $this->search . '%')
            ->latest()
            ->paginate(10);

        return view('livewire.admin.feedback-form.index', ['forms' => $forms])
            ->layout('layouts.app');
    }
}
