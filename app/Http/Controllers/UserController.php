<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if ($user->role === 'admin') {
            $users = User::all();
            return view('users.index', compact('users'));
        } else {
            return view('users.index');
        }
    }

    public function store(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Akses ditolak.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'ends_with:@gmail.com', 'unique:users,email'],
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,staff',
        ], [
            'email.ends_with' => 'Email harus menggunakan domain @gmail.com',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('users.index')->with('success', 'Akun berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $userToUpdate = User::findOrFail($id);
        $currentUser = auth()->user();

        // Staff can only edit themselves
        if ($currentUser->role !== 'admin' && $currentUser->id !== $userToUpdate->id) {
            abort(403, 'Akses ditolak.');
        }

        $rules = [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'ends_with:@gmail.com', Rule::unique('users', 'email')->ignore($userToUpdate->id)],
        ];

        // Role can only be changed by admin AND if it's not their own account
        if ($currentUser->role === 'admin' && $currentUser->id !== $userToUpdate->id) {
            $rules['role'] = 'required|in:admin,staff';
        }

        if ($request->filled('password')) {
            $rules['password'] = 'string|min:6';
        }

        $validated = $request->validate($rules, [
            'email.ends_with' => 'Email harus menggunakan domain @gmail.com',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        // Apply role only if admin editing others
        if ($currentUser->role === 'admin' && $currentUser->id !== $userToUpdate->id) {
            $updateData['role'] = $validated['role'];
        }

        $userToUpdate->update($updateData);

        return back()->with('success', 'Profil akun berhasil diperbarui.');
    }

    public function destroy($id)
    {
        return $this->toggleStatus($id);
    }

    public function toggleStatus($id)
    {
        $currentUser = auth()->user();
        
        if ($currentUser->role !== 'admin') {
            abort(403, 'Akses ditolak.');
        }

        $userToToggle = User::findOrFail($id);

        if ($userToToggle->role === 'admin') {
            abort(403, 'Sistem menolak penonaktifan akun berlevel Administrator.');
        }

        $userToToggle->is_active = !$userToToggle->is_active;
        $userToToggle->save();

        $statusText = $userToToggle->is_active ? 'diaktifkan kembali' : 'dinonaktifkan';
        return back()->with('success', "Akun staff '{$userToToggle->name}' berhasil {$statusText}.");
    }
}
