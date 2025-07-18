<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LCOLog;
use App\Models\ProductDistribution;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MonitoringController extends Controller
{
    /**
     * Halaman utama monitoring.
     */
    public function index()
    {
        $logs = LCOLog::orderBy('date')->get()->map(function ($item) {
            $item->date = Carbon::parse($item->date);
            return $item;
        });

        // 📅 Aktivasi harian
        $barChartData = $logs->groupBy(fn($item) => $item->date->format('Y-m-d'))->map(function ($group) {
            return [
                'day' => $group->first()->date->translatedFormat('l'),
                'date' => $group->first()->date->format('Y-m-d'),
                'done' => $group->sum('done'),
                'avg_jam' => $group->avg('jam'),
            ];
        })->values();

        //  Daily progress (terbaru)
        $latestDate = $logs->max('date');
        $latestLogs = $logs->filter(fn($item) => $item->date->equalTo($latestDate));
        $totalDone = $latestLogs->sum('done');
        $totalTarget = $latestLogs->sum('target');
        $totalTarget = $totalTarget > 0 ? $totalTarget : 1;

        //  Distribusi produk
        $productDist = ProductDistribution::select('label', 'jumlah')->get();

        //  Weekly Report
        $weeklyReport = $logs->groupBy(function ($item) {
            $week = $item->date->weekOfMonth;
            $month = $item->date->translatedFormat('M');
            return "W{$week} {$month}";
        })->map(function ($group) {
            $date = $group->first()->date;
            return [
                'label' => 'W' . $date->weekOfMonth . ' ' . $date->translatedFormat('M'),
                'done' => $group->sum('done'),
                'not_yet' => $group->sum('target') - $group->sum('done'),
            ];
        })->values();

        //  Rata-rata jam pemasangan
        $workingLogs = $logs->groupBy(fn($item) => $item->date->format('Y-m-d'))->map(function ($group) {
            $date = $group->first()->date;
            return [
                'date' => $date->format('Y-m-d'),
                'day' => $date->translatedFormat('l'),
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
            'workingLogs'
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

        // Update distribusi produk 20mb
        $existing20 = ProductDistribution::firstOrCreate(['label' => '20mb'], ['jumlah' => 0]);
        $existing20->jumlah += $validated['mb20'];
        $existing20->save();

        // Update distribusi produk 50mb
        $existing50 = ProductDistribution::firstOrCreate(['label' => '50mb'], ['jumlah' => 0]);
        $existing50->jumlah += $validated['mb50'];
        $existing50->save();

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
