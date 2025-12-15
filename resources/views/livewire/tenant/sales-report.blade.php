<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <h2 class="text-2xl font-semibold text-gray-800 mb-6">Laporan Penjualan</h2>

        {{-- KPI Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6 border-l-4 border-blue-500">
                <div class="text-gray-500 text-sm font-medium uppercase">Total Pendapatan</div>
                <div class="mt-2 text-3xl font-bold text-gray-900">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
            </div>
            <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6 border-l-4 border-green-500">
                <div class="text-gray-500 text-sm font-medium uppercase">Pesanan Sukses</div>
                <div class="mt-2 text-3xl font-bold text-gray-900">{{ $totalOrders }}</div>
            </div>
            <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6 border-l-4 border-purple-500">
                <div class="text-gray-500 text-sm font-medium uppercase">Saldo Tenant</div>
                <div class="mt-2 text-3xl font-bold text-gray-900">Rp {{ number_format(Auth::user()->tenant->balance, 0, ',', '.') }}</div>
                <button class="mt-2 text-xs text-blue-600 hover:underline">Request Withdrawal</button>
            </div>
        </div>

        {{-- Chart --}}
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Grafik Pendapatan (7 Hari Terakhir)</h3>
            <div id="revenueChart"></div>
        </div>
    </div>

    {{-- Inisialisasi ApexCharts --}}
    <script>
        document.addEventListener('livewire:initialized', () => {
            var options = {
                series: [{
                    name: 'Pendapatan',
                    data: @json($chartSeries)
                }],
                chart: {
                    height: 350,
                    type: 'area',
                    toolbar: {
                        show: false
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth'
                },
                xaxis: {
                    categories: @json($chartCategories),
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return "Rp " + new Intl.NumberFormat('id-ID').format(val)
                        }
                    }
                },
                colors: ['#3b82f6']
            };

            var chart = new ApexCharts(document.querySelector("#revenueChart"), options);
            chart.render();
        });
    </script>
</div>