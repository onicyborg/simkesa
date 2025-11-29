<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $students = Student::with(['schoolClass.batch', 'user'])->orderBy('full_name')->get();
        $classes = SchoolClass::with('batch')->orderByDesc('created_at')->get();
        return view('admin.students.index', compact('students', 'classes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:150'],
            'nis' => ['required', 'string', 'max:100', 'unique:students,nis'],
            'class_id' => ['required', 'uuid', 'exists:classes,id'],
            'parent_name' => ['nullable', 'string', 'max:150'],
            'parent_email' => ['nullable', 'email', 'max:150'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'photo' => ['nullable','image','mimes:jpg,jpeg,png,webp','max:2048'],
        ]);

        // Create linked user account
        $user = new User();
        $user->name = $validated['full_name'];
        $user->email = $validated['email'];
        $user->password = Hash::make($validated['password']);
        $user->role = 'student';
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('avatar', 'public');
            $user->profile_photo_path = $path;
        }
        $user->save();

        Student::create([
            'user_id' => $user->id,
            'full_name' => $validated['full_name'],
            'nis' => $validated['nis'],
            'class_id' => $validated['class_id'],
            'parent_name' => $validated['parent_name'] ?? null,
            'parent_email' => $validated['parent_email'] ?? null,
        ]);

        return redirect()->route('students.index')->with('success', 'Siswa berhasil ditambahkan');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $student = Student::with('user')->findOrFail($id);

        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:150'],
            'nis' => ['required', 'string', 'max:100', 'unique:students,nis,' . $student->id . ',id'],
            'class_id' => ['required', 'uuid', 'exists:classes,id'],
            'parent_name' => ['nullable', 'string', 'max:150'],
            'parent_email' => ['nullable', 'email', 'max:150'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email,' . optional($student->user)->id . ',id'],
            'password' => ['nullable', 'string', 'min:8'],
            'photo' => ['nullable','image','mimes:jpg,jpeg,png,webp','max:2048'],
        ]);

        // Update linked user
        $user = $student->user ?: new User(['role' => 'student']);
        $user->name = $validated['full_name'];
        $user->email = $validated['email'];
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }
        if ($request->hasFile('photo')) {
            if (!empty($user->profile_photo_path) && Storage::disk('public')->exists($user->profile_photo_path)) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            $path = $request->file('photo')->store('avatar', 'public');
            $user->profile_photo_path = $path;
        }
        $user->role = 'student';
        $user->save();

        // Ensure relation
        if ($student->user_id !== $user->id) {
            $student->user_id = $user->id;
        }

        $student->update([
            'full_name' => $validated['full_name'],
            'nis' => $validated['nis'],
            'class_id' => $validated['class_id'],
            'parent_name' => $validated['parent_name'] ?? null,
            'parent_email' => $validated['parent_email'] ?? null,
        ]);

        return redirect()->route('students.index')->with('success', 'Siswa berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $student = Student::findOrFail($id);
        $student->delete();
        return redirect()->route('students.index')->with('success', 'Siswa berhasil dihapus');
    }
}
