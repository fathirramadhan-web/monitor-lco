<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LCOLog;
use App\Models\ProductDistribution;

class DataController extends Controller
{
    /**
     * Menampilkan semua data LCOLog dan ProductDistribution
     */
    public function index()
    {
        return view('admin.data.index', [
            'logs' => LCOLog::all(),
            'products' => ProductDistribution::all(),
        ]);
    }

    /**
     * Menampilkan form edit berdasarkan model dan id
     */
    public function edit($model, $id)
    {
        $data = $this->resolveModel($model)::findOrFail($id);

        return view('admin.data.edit', [
            'model' => $model,
            'data' => $data,
        ]);
    }

    /**
     * Menyimpan perubahan dari form edit
     */
    public function update(Request $request, $model, $id)
    {
        $modelClass = $this->resolveModel($model);
        $data = $modelClass::findOrFail($id);

        $data->fill($request->all());
        $data->save();

        return redirect()->route('admin.data.index')->with('success', 'Data berhasil diperbarui.');
    }

    /**
     * Menentukan model berdasarkan string nama model
     */
    private function resolveModel($model)
    {
        return match ($model) {
            'lcolog' => \App\Models\LCOLog::class,
            'productdistribution' => \App\Models\ProductDistribution::class,
            default => abort(404),
        };
    }
}
