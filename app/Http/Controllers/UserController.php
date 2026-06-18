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
            'username' => 'required|string|max:255|unique:users,username',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,staff',
        ]);

        User::create([
            'name' => $request->name,
            'username' => $request->username,
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
            'username' => ['required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($userToUpdate->id)],
        ];

        // Role can only be changed by admin AND if it's not their own account
        if ($currentUser->role === 'admin' && $currentUser->id !== $userToUpdate->id) {
            $rules['role'] = 'required|in:admin,staff';
        }

        if ($request->filled('password')) {
            $rules['password'] = 'string|min:6';
        }

        $validated = $request->validate($rules);

        $updateData = [
            'name' => $validated['name'],
            'username' => $validated['username'],
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
        $currentUser = auth()->user();
        
        if ($currentUser->role !== 'admin') {
            abort(403, 'Akses ditolak.');
        }

        $userToDelete = User::findOrFail($id);

        if ($userToDelete->role === 'admin') {
            abort(403, 'Sistem menolak penghapusan akun berlevel Administrator.');
        }

        $userToDelete->delete();

        return back()->with('success', 'Akun staff berhasil dihapus.');
    }
}
