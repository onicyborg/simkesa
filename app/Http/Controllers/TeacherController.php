<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class TeacherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $teachers = User::with('homeroomClasses')
            ->where('role', 'teacher')
            ->orderBy('name')
            ->get();

        return view('teachers.index', compact('teachers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'photo' => ['nullable','image','mimes:jpg,jpeg,png,webp','max:2048'],
        ]);

        $user = new User();
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->password = Hash::make($validated['password']);
        $user->role = 'teacher';
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('avatar', 'public');
            $user->profile_photo_path = $path;
        }
        $user->save();

        return redirect()->route('teachers.index')->with('success', 'Guru berhasil ditambahkan');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::where('role', 'teacher')->findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100', 'unique:users,email,' . $user->id . ',id'],
            'password' => ['nullable', 'string', 'min:8'],
            'photo' => ['nullable','image','mimes:jpg,jpeg,png,webp','max:2048'],
        ]);

        $user->name = $validated['name'];
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
        $user->save();

        return redirect()->route('teachers.index')->with('success', 'Guru berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::where('role', 'teacher')->findOrFail($id);

        // Delete photo if exists
        if (!empty($user->profile_photo_path) && Storage::disk('public')->exists($user->profile_photo_path)) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }

        // Set wali kelas di kelas terkait menjadi null sebelum hapus guru
        SchoolClass::where('homeroom_teacher_id', $user->id)->update(['homeroom_teacher_id' => null]);

        $user->delete();

        return redirect()->route('teachers.index')->with('success', 'Guru berhasil dihapus');
    }
}
