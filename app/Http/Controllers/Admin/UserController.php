<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // 1. Menampilkan Daftar Pengguna
    // 1. Menampilkan Daftar Pengguna (Dengan Filter Canggih)
    public function index(Request $request)
    {
        $query = User::query();

        // A. Filter Pencarian Teks (Nama atau Email)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // B. Filter Hak Akses (Admin / Teacher)
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // C. Filter Status (Aktif / Nonaktif)
        if ($request->filled('status')) {
            // Kita ubah nilai '1' atau '0' dari form menjadi boolean (true/false)
            $isActive = $request->status === '1' ? true : false;
            $query->where('is_active', $isActive);
        }

        // withQueryString() memastikan saat pindah halaman (pagination), filternya tidak hilang
        $users = $query->orderBy('name', 'asc')->paginate(10)->withQueryString();

        return view('admin.user.index', compact('users'));
    }

    // 2. Menampilkan Form Tambah
    public function create()
    {
        return view('admin.user.form');
    }

    // 3. Menyimpan Data Pengguna Baru
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,teacher' // Pastikan tabel users Anda punya kolom role
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Password wajib di-hash demi keamanan
            'role' => $request->role
        ]);

        return redirect()->route('admin.user.index')->with('success', 'Akun pengguna berhasil ditambahkan!');
    }

    // 4. Menampilkan Form Edit
    public function edit(User $user)
    {
        return view('admin.user.form', compact('user'));
    }

    // 5. Menyimpan Perubahan Data
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            // Validasi email unik, TAPI abaikan email milik user ini sendiri
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:6', // Password opsional saat edit
            'role' => 'required|in:admin,teacher'
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role
        ];

        // Jika form password diisi, berarti admin ingin mereset password user tersebut
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.user.index')->with('success', 'Data pengguna berhasil diperbarui!');
    }

    // 6. Menonaktifkan / Mengaktifkan Pengguna (Toggle Status)
    public function destroy(User $user)
    {
        // Proteksi mutlak: Admin tidak boleh menonaktifkan dirinya sendiri
        if ($user->id === auth()->id()) {
            return redirect()->back()->withErrors(['Tindakan ditolak! Anda tidak bisa menonaktifkan akun Anda sendiri.']);
        }

        // Membalikkan status (True jadi False, False jadi True)
        $user->update([
            'is_active' => !$user->is_active
        ]);

        $statusMessage = $user->is_active ? 'diaktifkan kembali' : 'dinonaktifkan';

        return redirect()->route('admin.user.index')->with('success', "Akun pengguna berhasil {$statusMessage}!");
    }
}
