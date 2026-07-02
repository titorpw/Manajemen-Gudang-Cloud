<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Mutation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $lowStockItems = Item::whereColumn('stock', '<=', 'stock_limit')
            ->orderBy('stock', 'asc')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'kode_barang' => $item->code,
                    'nama_barang' => $item->name,
                    'stok_saat_ini' => $item->stock,
                    'batas_stok' => $item->stock_limit,
                    'lokasi_rak' => $item->rack_location,
                ];
            });

        $startDate = Carbon::now()->subDays(30)->startOfDay();
        $endDate = Carbon::now()->endOfDay();

        $trendMutasi = Mutation::select(
            DB::raw('DATE(created_at) as date'),
            'type',
            DB::raw('SUM(quantity) as total'),
        )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy(DB::raw('DATE(created_at)'), 'type')
            ->orderBy('date', 'asc')
            ->get();

        $dates = [];
        $dataIN = [];
        $dataOUT = [];

        for ($i = 0; $i <= 30; $i++) {
            $dateStr = Carbon::now()
                ->subDays(30 - $i)
                ->format('Y-m-d');
            $dates[$dateStr] = $dateStr;
            $dataIN[$dateStr] = 0;
            $dataOUT[$dateStr] = 0;
        }

        foreach ($trendMutasi as $mutasi) {
            if ($mutasi->type === 'IN') {
                $dataIN[$mutasi->date] = (int) $mutasi->total;
            } elseif ($mutasi->type === 'OUT') {
                $dataOUT[$mutasi->date] = (int) $mutasi->total;
            }
        }

        return response()->json([
            'stok_menipis' => $lowStockItems,
            'grafik_mutasi' => [
                'labels' => array_values($dates),
                'dataset_in' => array_values($dataIN),
                'dataset_out' => array_values($dataOUT),
            ],
            'ringkasan' => [
                'total_jenis_barang' => Item::count(),
                'total_stok_keseluruhan' => Item::sum('stock'),
                'total_mutasi_hari_ini' => Mutation::whereDate(
                    'created_at',
                    Carbon::today(),
                )->count(),
            ],
        ]);
    }
}
