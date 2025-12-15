<?php

namespace App\Livewire\Tenant;

use App\Models\ProductOrder;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class OrderManager extends Component
{
    use WithPagination;

    public $search = '';
    public $filterStatus = '';

    // Detail Modal
    public $selectedOrder;
    public $isModalOpen = false;

    // Input Resi
    public $trackingNumber;

    public function render()
    {
        $tenantId = Auth::user()->tenant->id;

        $orders = ProductOrder::where('tenant_id', $tenantId)
            ->when($this->search, function ($q) {
                $q->where('id', 'like', '%' . $this->search . '%')
                    ->orWhereHas('user', function ($u) {
                        $u->where('name', 'like', '%' . $this->search . '%');
                    });
            })
            ->when($this->filterStatus, function ($q) {
                $q->where('status', $this->filterStatus);
            })
            ->with(['user', 'items.product']) // Eager load
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.tenant.order-manager', [
            'orders' => $orders
        ])->layout('layouts.app');
    }

    public function showDetails($orderId)
    {
        $this->selectedOrder = ProductOrder::with('items.product')->find($orderId);
        $this->trackingNumber = $this->selectedOrder->tracking_number;
        $this->isModalOpen = true;
    }

    public function updateStatus($status)
    {
        if (!$this->selectedOrder) return;

        // Validasi khusus jika status = shipped (dikirim)
        if ($status === 'shipped') {
            $this->validate([
                'trackingNumber' => 'required|string|min:3'
            ], [
                'trackingNumber.required' => 'Nomor Resi wajib diisi jika barang dikirim.'
            ]);

            $this->selectedOrder->tracking_number = $this->trackingNumber;
        }

        $this->selectedOrder->update(['status' => $status]);

        // Notifikasi
        $this->dispatch('swal:success', message: "Status pesanan diperbarui menjadi: " . ucfirst($status));
        $this->isModalOpen = false;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->selectedOrder = null;
    }
}
