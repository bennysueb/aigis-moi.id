<?php

namespace App\Livewire\Admin\Voucher;

use App\Models\Voucher;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $isEditMode = false;

    // Form Properties
    public $voucherId;
    public $code;
    public $type = 'fixed_amount'; // Default
    public $amount = 0;
    public $min_purchase_amount = 0;
    public $usage_limit = 100;
    public $usage_per_user = 1;
    public $valid_from;
    public $valid_until;
    public $is_active = true;

    // Reset Pagination saat search
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $vouchers = Voucher::query()
            ->when($this->search, function ($q) {
                $q->where('code', 'like', '%' . $this->search . '%');
            })
            ->withCount('usages') // Hitung berapa kali dipakai
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.admin.voucher.index', [
            'vouchers' => $vouchers
        ])->layout('layouts.app');
    }

    public function create()
    {
        $this->resetForm();
        $this->isEditMode = false;
        $this->showModal = true;
    }

    public function edit($id)
    {
        $voucher = Voucher::findOrFail($id);

        $this->voucherId = $voucher->id;
        $this->code = $voucher->code;
        $this->type = $voucher->type;
        $this->amount = $voucher->amount;
        $this->min_purchase_amount = $voucher->min_purchase_amount;
        $this->usage_limit = $voucher->usage_limit;
        $this->usage_per_user = $voucher->usage_per_user;
        // Format tanggal untuk input HTML datetime-local
        $this->valid_from = $voucher->valid_from ? $voucher->valid_from->format('Y-m-d\TH:i') : null;
        $this->valid_until = $voucher->valid_until ? $voucher->valid_until->format('Y-m-d\TH:i') : null;
        $this->is_active = (bool) $voucher->is_active;

        $this->isEditMode = true;
        $this->showModal = true;
    }

    public function save()
    {
        $rules = [
            'code' => ['required', 'string', 'max:50', Rule::unique('vouchers', 'code')->ignore($this->voucherId)],
            'type' => 'required|in:fixed_amount,percentage',
            'amount' => 'required|numeric|min:0',
            'min_purchase_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'required|integer|min:0',
            'usage_per_user' => 'required|integer|min:1',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after_or_equal:valid_from',
            'is_active' => 'boolean'
        ];

        $this->validate($rules);

        $data = [
            'code' => strtoupper($this->code), // Paksa Huruf Besar
            'type' => $this->type,
            'amount' => $this->amount,
            'min_purchase_amount' => $this->min_purchase_amount ?? 0,
            'usage_limit' => $this->usage_limit,
            'usage_per_user' => $this->usage_per_user,
            'valid_from' => $this->valid_from,
            'valid_until' => $this->valid_until,
            'is_active' => $this->is_active,
        ];

        if ($this->isEditMode) {
            Voucher::find($this->voucherId)->update($data);
            $message = 'Voucher berhasil diperbarui.';
        } else {
            Voucher::create($data);
            $message = 'Voucher berhasil dibuat.';
        }

        $this->showModal = false;
        $this->resetForm();
        $this->dispatch('swal:success', message: $message);
    }

    public function toggleStatus($id)
    {
        $voucher = Voucher::findOrFail($id);
        $voucher->update(['is_active' => !$voucher->is_active]);
        $this->dispatch('swal:success', message: 'Status voucher diubah.');
    }

    public function delete($id)
    {
        Voucher::findOrFail($id)->delete();
        $this->dispatch('swal:success', message: 'Voucher dihapus.');
    }

    private function resetForm()
    {
        $this->reset(['voucherId', 'code', 'type', 'amount', 'min_purchase_amount', 'usage_limit', 'usage_per_user', 'valid_from', 'valid_until', 'is_active']);
    }
}
