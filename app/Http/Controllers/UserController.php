<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    // Tampilkan Daftar User
    public function index()
    {
        // Ambil semua user, urutkan admin dulu baru staff
        $users = User::orderBy('role', 'asc')->orderBy('name', 'asc')->get();
        return view('users.index', compact('users'));
    }

    // Simpan User Baru
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:admin,staff',
            'password' => 'required|min:6',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'User baru berhasil ditambahkan!');
    }

    // Update User (Ganti Role / Password)
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$id,
            'role' => 'required|in:admin,staff',
            'password' => 'nullable|min:6', // Password boleh kosong kalau gak mau diganti
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ];

        // Jika password diisi, kita hash dan update. Jika kosong, biarkan password lama.
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return back()->with('success', 'Data user berhasil diperbarui!');
    }

    // Hapus User
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Gunakan Auth::id() agar VS Code tidak error
        if (Auth::id() == $id) {
            return back()->with('error', 'Anda tidak bisa menghapus akun sendiri!');
        }

        $user->delete();

        return back()->with('success', 'User berhasil dihapus.');
    }
}