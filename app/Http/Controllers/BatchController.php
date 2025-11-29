<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use Illuminate\Http\Request;

class BatchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $batches = Batch::orderByDesc('year')->orderBy('name')->get();
        return view('batches.index', compact('batches'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required','string','max:50'],
            'year' => ['required','integer','between:1900,3000'],
        ]);

        Batch::create($validated);

        return redirect()->route('batches.index')->with('success', 'Angkatan berhasil ditambahkan');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => ['required','string','max:50'],
            'year' => ['required','integer','between:1900,3000'],
        ]);

        $batch = Batch::findOrFail($id);
        $batch->update($validated);

        return redirect()->route('batches.index')->with('success', 'Angkatan berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $batch = Batch::findOrFail($id);
        $batch->delete();

        return redirect()->route('batches.index')->with('success', 'Angkatan berhasil dihapus');
    }
}
