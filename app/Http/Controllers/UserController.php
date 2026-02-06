<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role; // Import Model Role Spatie

class UserController extends Controller
{
    // 1. INDEX: TAMPILKAN LIST USER
    public function index(Request $request)
    {
        $query = User::with('roles');

        // 1. Filter Pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // 2. Filter Role
        if ($request->filled('role')) {
            $query->role($request->role);
        }

        $users = $query->latest()->paginate(10)->withQueryString();
        
        // Ambil daftar role yang ADA di database untuk dropdown
        $roles = Role::pluck('name');

        // 3. Statistik (PERBAIKAN: Hanya hitung role yang eksis)
        $stats = [
            'total' => User::count(),
            // Cek apakah role 'admin' ada sebelum menghitung, atau hitung manual
            // Disini kita gabung super_admin dan admin sebagai "Level Admin"
            'admins' => User::whereHas('roles', function($q) {
                $q->whereIn('name', ['super_admin', 'admin']);
            })->count(),
            
            'staff' => User::whereHas('roles', function($q) {
                $q->where('name', 'staff');
            })->count(),
        ];

        return view('users.index', compact('users', 'roles', 'stats'));
    }

    // 2. CREATE: HALAMAN TAMBAH USER
    public function create()
    {
        // Ambil semua role dari database (kecuali super_admin jika mau disembunyikan, tapi kita tampilkan saja)
        $roles = Role::pluck('name', 'name')->all();
        
        return view('users.create', compact('roles'));
    }

    // 3. STORE: PROSES SIMPAN USER
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required'], // Role wajib dipilih
        ]);

        // Buat User Baru
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            // Kita tidak simpan kolom 'role' manual lagi, biarkan null atau default
        ]);

        // ASSIGN ROLE SPATIE
        $user->assignRole($request->role);

        return redirect()->route('users.index')->with('success', 'User baru berhasil ditambahkan.');
    }

    // 4. EDIT: HALAMAN EDIT USER
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::pluck('name', 'name')->all();
        
        // Ambil role user saat ini (untuk auto-select di form)
        $userRole = $user->roles->pluck('name')->first();

        return view('users.edit', compact('user', 'roles', 'userRole'));
    }

    // 5. UPDATE: PROSES UPDATE USER
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'role' => ['required'],
        ]);

        $input = $request->only(['name', 'email']);
        
        // Update password hanya jika diisi
        if ($request->filled('password')) {
            $input['password'] = Hash::make($request->password);
        }

        $user->update($input);

        // UPDATE ROLE (Hapus role lama, pasang role baru)
        $user->syncRoles([$request->role]);

        return redirect()->route('users.index')->with('success', 'Data user berhasil diperbarui.');
    }

    // 6. DESTROY: HAPUS USER
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Proteksi: Jangan hapus diri sendiri
        if (auth()->id() == $user->id) {
            return back()->with('error', 'Anda tidak bisa menghapus akun sendiri!');
        }

        // Proteksi: Jangan hapus Super Admin sembarangan (Opsional)
        if ($user->hasRole('super_admin')) {
             return back()->with('error', 'Akun Super Admin tidak boleh dihapus.');
        }

        $user->delete();
        return back()->with('success', 'User berhasil dihapus.');
    }
}