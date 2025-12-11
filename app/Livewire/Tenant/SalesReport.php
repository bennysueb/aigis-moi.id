<?php

namespace App\Livewire\Tenant;

use App\Models\ProductOrder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class SalesReport extends Component
{
    public $totalRevenue = 0;
    public $totalOrders = 0;
    public $productsSold = 0;

    public function render()
    {
        $tenantId = Auth::user()->tenant->id;

        // 1. KPI Cards Data
        $this->totalRevenue = ProductOrder::where('tenant_id', $tenantId)
            ->whereIn('status', ['paid', 'processing', 'shipped', 'completed'])
            ->sum('final_amount');

        $this->totalOrders = ProductOrder::where('tenant_id', $tenantId)
            ->whereIn('status', ['paid', 'processing', 'shipped', 'completed'])
            ->count();

        // 2. Data Grafik (Penjualan 7 Hari Terakhir)
        $salesData = ProductOrder::where('tenant_id', $tenantId)
            ->whereIn('status', ['paid', 'processing', 'shipped', 'completed'])
            ->where('created_at', '>=', now()->subDays(7))
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(final_amount) as total')
            )
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        $chartCategories = $salesData->pluck('date')->map(fn($date) => \Carbon\Carbon::parse($date)->format('d M'))->toArray();
        $chartSeries = $salesData->pluck('total')->toArray();

        return view('livewire.tenant.sales-report', [
            'chartCategories' => $chartCategories,
            'chartSeries' => $chartSeries
        ])->layout('layouts.app');
    }
}
