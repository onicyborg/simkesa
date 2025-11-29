<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Http\Request;

class SchoolClassController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $classes = SchoolClass::with(['batch', 'homeroomTeacher'])
            ->orderByDesc('created_at')
            ->get();

        $batches = Batch::orderByDesc('year')->orderBy('name')->get();
        $teachers = User::where('role', 'teacher')->orderBy('name')->get();

        return view('admin.classes.index', compact('classes', 'batches', 'teachers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'batch_id' => ['required', 'uuid', 'exists:batches,id'],
            'homeroom_teacher_id' => ['nullable', 'uuid', 'exists:users,id'],
        ]);

        // Optional: ensure selected homeroom teacher has role teacher
        if (!empty($validated['homeroom_teacher_id'])) {
            $isTeacher = User::where('id', $validated['homeroom_teacher_id'])->where('role', 'teacher')->exists();
            if (!$isTeacher) {
                return back()->with('error', 'Wali kelas harus berperan sebagai teacher.')->withInput();
            }
        }

        SchoolClass::create($validated);

        return redirect()->route('classes.index')->with('success', 'Kelas berhasil ditambahkan');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'batch_id' => ['required', 'uuid', 'exists:batches,id'],
            'homeroom_teacher_id' => ['nullable', 'uuid', 'exists:users,id'],
        ]);

        if (!empty($validated['homeroom_teacher_id'])) {
            $isTeacher = User::where('id', $validated['homeroom_teacher_id'])->where('role', 'teacher')->exists();
            if (!$isTeacher) {
                return back()->with('error', 'Wali kelas harus berperan sebagai teacher.')->withInput();
            }
        }

        $class = SchoolClass::findOrFail($id);
        $class->update($validated);

        return redirect()->route('classes.index')->with('success', 'Kelas berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $class = SchoolClass::withCount('students')->findOrFail($id);
        if ($class->students_count > 0) {
            return redirect()->route('classes.index')->with('error', 'Tidak dapat menghapus: masih ada siswa pada kelas ini.');
        }

        $class->delete();

        return redirect()->route('classes.index')->with('success', 'Kelas berhasil dihapus');
    }
}
