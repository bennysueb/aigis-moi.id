<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <h2 class="text-2xl font-semibold text-gray-800 mb-6">Manajemen Pesanan Masuk</h2>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

            {{-- Filters --}}
            <div class="flex flex-col md:flex-row gap-4 mb-6 justify-between">
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari ID Order / Nama Pembeli..." class="border-gray-300 rounded-md shadow-sm">

                <select wire:model.live="filterStatus" class="border-gray-300 rounded-md shadow-sm">
                    <option value="">Semua Status</option>
                    <option value="paid">Paid (Perlu Proses)</option>
                    <option value="processing">Processing (Sedang Dikemas)</option>
                    <option value="shipped">Shipped (Dikirim)</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pembeli</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($orders as $order)
                        <tr>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">#{{ substr($order->id, 0, 8) }}...</td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $order->user->name }}<br>
                                <span class="text-xs text-gray-400">{{ $order->created_at->format('d M Y H:i') }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm font-bold text-gray-900">
                                Rp {{ number_format($order->final_amount, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($order->status == 'paid') bg-green-100 text-green-800
                                    @elseif($order->status == 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($order->status == 'cancelled') bg-red-100 text-red-800
                                    @else bg-blue-100 text-blue-800 @endif">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right text-sm font-medium">
                                <button wire:click="showDetails('{{ $order->id }}')" class="text-indigo-600 hover:text-indigo-900">Kelola</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-4">{{ $orders->links() }}</div>
            </div>
        </div>
    </div>

    {{-- MODAL DETAIL ORDER --}}
    @if($isModalOpen && $selectedOrder)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Detail Pesanan</h3>

                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div>
                            <p class="text-sm text-gray-500">Pembeli</p>
                            <p class="font-bold">{{ $selectedOrder->user->name }}</p>
                            <p class="text-sm">{{ $selectedOrder->user->email }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Alamat Pengiriman</p>
                            <p class="text-sm">{{ $selectedOrder->shipping_address }}</p>
                        </div>
                    </div>

                    <table class="min-w-full divide-y divide-gray-200 mb-6">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Produk</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Qty</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($selectedOrder->items as $item)
                            <tr>
                                <td class="px-4 py-2 text-sm">{{ $item->product->name }}</td>
                                <td class="px-4 py-2 text-sm text-right">{{ $item->quantity }}</td>
                                <td class="px-4 py-2 text-sm text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-gray-50 font-bold">
                                <td colspan="2" class="px-4 py-2 text-right">Total Bayar</td>
                                <td class="px-4 py-2 text-right">Rp {{ number_format($selectedOrder->final_amount, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>

                    {{-- Action Buttons --}}
                    <div class="border-t pt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Update Status & Resi</label>

                        <div class="flex gap-2 items-end">
                            <div class="flex-1">
                                <input type="text" wire:model="trackingNumber" placeholder="Masukkan No. Resi (Jika dikirim)" class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                                @error('trackingNumber') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            @if($selectedOrder->status == 'paid')
                            <button wire:click="updateStatus('processing')" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded text-sm">Proses Pesanan</button>
                            @endif

                            @if($selectedOrder->status == 'processing')
                            <button wire:click="updateStatus('shipped')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm">Kirim Barang</button>
                            @endif

                            @if($selectedOrder->status == 'shipped')
                            <button wire:click="updateStatus('completed')" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm">Selesai</button>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" wire:click="closeModal" class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:ml-3 sm:w-auto sm:text-sm">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <script>
        document.addEventListener('livewire:initialized', () => {
            @this.on('swal:success', (event) => {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: event.message,
                    timer: 1500,
                    showConfirmButton: false
                });
            });
        });
    </script>
</div>