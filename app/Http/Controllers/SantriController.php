<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Santri;
use App\Models\Kelasnya;
use App\Models\Kelompok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SantriController extends Controller
{
    public function index()
    {
        $santris = Santri::all();
        $kelompoks = Kelompok::all();
        $kelasnyas = Kelasnya::all();
        return view('santris.index',compact('santris','kelompoks','kelasnyas'));
    }

    public function store(Request $request)
    {
         // 1. Validasi input
         $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'kelompok_id'   => 'required','integer',
            'kelas_id'      => 'required','integer',
            'kelamin'       => 'required|in:laki-laki,perempuan',
            'email'         => 'required|email|unique:users,email',
            'password'      => 'required|string|min:6',
            'avatar'        => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048', // Avatar harus berupa gambar dan batas ukuran 2MB
        ]);

        try {
            DB::transaction(function () use ($validated,$request) {
    
                $avatarPath = $validated['avatar'] ?? 'avatar/default-avatar.png';

                // Jika ada avatar yang diunggah, simpan gambar tersebut
                if ($validated['avatar']) {
                    $avatarPath = $request->file('avatar')->store('avatars', 'public');
                }

                // 2. Buat user baru
                $user = User::create([
                    'name'      => $validated['name'],
                    'email'     => $validated['email'],
                    'avatar'    => $avatarPath,
                    'password'  => Hash::make($validated['password']),
                ]);
    
               
                $user->assignRole('siswa');
    
                // 4. Simpan ke tabel santri
                Santri::create([
                    'user_id'       => $user->id,
                    'kelompok_id'   => $validated['kelompok_id'],
                    'kelas_id'      => $validated['kelas_id'],
                    'kelamin'       => $validated['kelamin'],
                ]);
            });
    
            // 5. Redirect dengan flash message (dipakai SweetAlert2 di view)
            return redirect()
                ->route('santris')
                ->with('success', 'Data santri berhasil disimpan!');
    
        } catch (\Exception $e) {
            return redirect()
                ->route('santris')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function edit(Santri $santri)
    {
        $santriView = Santri::all();
        $kelompoks = Kelompok::all();
        $kelasnyas = Kelasnya::all();
        return view('santris.edit',compact('santriView','kelompoks','kelasnyas','santri'));
    }

    public function update(Request $request, Santri $santri)
    {
        // 1. Validasi input
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'kelompok_id'   => 'required|integer',
            'kelas_id'      => 'required|integer',
            'kelamin'       => 'required|in:laki-laki,perempuan',
            'email'         => 'required|email|unique:users,email,' . $santri->user_id,  // Mengabaikan validasi untuk email yang sama
            'password'      => 'nullable|string|min:6', // Password opsional
            'avatar'        => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048', // Avatar opsional
        ]);

        try {
            DB::transaction(function () use ($validated, $request, $santri) {

                // Tentukan avatar: jika tidak ada avatar yang diunggah, set ke avatar lama
                $avatarPath = $santri->user->avatar ?? 'avatar/default-avatar.png';

                // Jika ada avatar yang diunggah, simpan gambar tersebut
                if ($request->hasFile('avatar')) {
                    // Hapus avatar lama jika ada
                    if ($santri->user->avatar && file_exists(public_path('storage/' . $santri->user->avatar))) {
                        unlink(public_path('storage/' . $santri->user->avatar)); // Menghapus file avatar lama
                    }
                    // Simpan avatar baru
                    $avatarPath = $request->file('avatar')->store('avatars', 'public');
                }

                // 2. Update data user
                $santri->user->update([
                    'name'      => $validated['name'],
                    'email'     => $validated['email'],
                    'avatar'    => $avatarPath,
                    'password'  => $validated['password'] ? Hash::make($validated['password']) : $santri->user->password, // Jika password kosong, gunakan password lama
                ]);

                // 3. Update data santri
                $santri->update([
                    'kelompok_id' => $validated['kelompok_id'],
                    'kelas_id'    => $validated['kelas_id'],
                    'kelamin'     => $validated['kelamin'],
                ]);
            });

            // 4. Redirect dengan flash message sukses
            return redirect()
                ->route('santris')
                ->with('success', 'Data Santri berhasil diperbarui!');

        } catch (\Exception $e) {
            // Jika terjadi kesalahan
            return redirect()
                ->route('santris')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        // Ambil data Ustadz berdasarkan ID
        $santri = Santri::with('user', 'kelasnya', 'kelompok')->findOrFail($id);

        // Mengirim data dalam bentuk JSON
        return response()->json([
            'name' => $santri->user->name,
            'kelasnya' => $santri->kelasnya->nama_kelas,
            'kelompok' => $santri->kelompok->nama_kelompok,
            'kelamin' => $santri->kelamin,
            'email' => $santri->user->email,
            'avatar' => asset('storage/' . ($santri->user->avatar ?: 'avatar/default-avatar.png'))
        ]);
    }

    public function destroy($id)
    {
        try {
            // Temukan data Santri berdasarkan ID
            $santri = Santri::with('user')->findOrFail($id);

            // Hapus avatar jika ada (Opsional)
            if ($santri->user->avatar && file_exists(public_path('storage/' . $santri->user->avatar))) {
                unlink(public_path('storage/' . $santri->user->avatar)); // Menghapus file avatar
            }

            // Soft delete data Santri
            $santri->delete();

            // Soft delete data User terkait
            $santri->user->delete();

            // Redirect dengan flash message sukses
            return redirect()->route('santris')
                ->with('success', 'Data Santri berhasil dihapus!');
        } catch (\Exception $e) {
            // Jika terjadi kesalahan
            return redirect()->route('santris')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

}
