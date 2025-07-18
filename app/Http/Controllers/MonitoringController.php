<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LCOLog;
use App\Models\ProductDistribution;
use Illuminate\Support\Facades\DB; // ✅ Tambahkan ini
use Carbon\Carbon;

class MonitoringController extends Controller
{
    /**
     * Halaman utama monitoring.
     */
    public function index()
    {
        // Ambil dan parsing date
        $logs = LCOLog::orderBy('date')->get()->map(function ($item) {
            $item->date = $item->date ? Carbon::parse($item->date) : null;
            return $item;
        })->filter(fn($item) => $item->date !== null); // Filter date null

        // 📅 Aktivasi harian (Bar Chart)
        $barChartData = $logs->groupBy(fn($item) => $item->date->format('Y-m-d'))->map(function ($group) {
            $date = optional($group->first())->date;
            return [
                'day' => $date ? $date->translatedFormat('l') : '-',
                'date' => $date ? $date->format('Y-m-d') : '-',
                'done' => $group->sum('done'),
                'avg_jam' => $group->avg('jam'),
            ];
        })->values();

        // 📈 Daily progress (Pie Chart - Tanggal Terbaru)
        $latestDate = $logs->max('date');
        $latestLogs = $logs->filter(fn($item) => $item->date->equalTo($latestDate));

        $dailyDone = $latestLogs->sum('done');
        $dailyTarget = $latestLogs->sum('target');
        $dailyRemaining = max($dailyTarget - $dailyDone, 0);

        $dailyProgress = [
            'done' => $dailyDone,
            'remaining' => $dailyRemaining,
            'label_date' => $latestDate ? $latestDate->format('Y-m-d') : '-',
        ];

        // 🎯 Total Activation (seluruh waktu)
        $totalDone = $logs->sum('done');
        $totalTarget = 100;

        // 📦 Distribusi produk
        $productDist = ProductDistribution::select('label', 'jumlah')->get();

        // 📊 Weekly Report
        $weeklyReport = $logs->groupBy(function ($item) {
            $date = optional($item->date);
            $week = $date?->weekOfMonth ?? 0;
            $month = $date?->translatedFormat('M') ?? '-';
            return "W{$week} {$month}";
        })->map(function ($group) {
            $date = optional($group->first())->date;
            return [
                'label' => $date ? 'W' . $date->weekOfMonth . ' ' . $date->translatedFormat('M') : 'Unknown',
                'done' => $group->sum('done'),
                'not_yet' => $group->sum('target') - $group->sum('done'),
            ];
        })->values();

        // ⏱️ Rata-rata jam pemasangan
        $workingLogs = $logs->groupBy(fn($item) => $item->date->format('Y-m-d'))->map(function ($group) {
            $date = optional($group->first())->date;
            return [
                'date' => $date ? $date->format('Y-m-d') : '-',
                'day' => $date ? $date->translatedFormat('l') : '-',
                'hours' => $group->sum('jam'),
            ];
        })->values();

        return view('monitoring.index', compact(
            'barChartData',
            'totalDone',
            'totalTarget',
            'latestDate',
            'productDist',
            'weeklyReport',
            'workingLogs',
            'dailyProgress'
        ));
    }

    /**
     * Form input monitoring.
     */
    public function create()
    {
        return view('monitoring.create');
    }

    /**
     * Simpan data monitoring.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'done' => 'required|integer|min:0',
            'target' => 'required|integer|min:0',
            'jam' => 'required|numeric|min:0',
            'mb20' => 'required|integer|min:0',
            'mb50' => 'required|integer|min:0',
        ]);

        // Simpan log monitoring
        LCOLog::create([
            'date' => $validated['date'],
            'done' => $validated['done'],
            'target' => $validated['target'],
            'jam' => $validated['jam'],
        ]);

        // Update distribusi produk
        ProductDistribution::updateOrCreate(
            ['label' => '20mb'],
            ['jumlah' => DB::raw("jumlah + {$validated['mb20']}")]
        );

        ProductDistribution::updateOrCreate(
            ['label' => '50mb'],
            ['jumlah' => DB::raw("jumlah + {$validated['mb50']}")]
        );

        return redirect()->route('monitoring')->with('success', 'Data berhasil ditambahkan.');
    }

    /**
     * Form edit data.
     */
    public function edit($id)
    {
        $log = LCOLog::findOrFail($id);
        return view('monitoring.edit', compact('log'));
    }

    /**
     * Update data monitoring.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'done' => 'required|integer|min:0',
            'target' => 'required|integer|min:0',
            'jam' => 'required|numeric|min:0',
        ]);

        $log = LCOLog::findOrFail($id);
        $log->update($validated);

        return redirect()->route('monitoring')->with('success', 'Data berhasil diperbarui.');
    }
}
