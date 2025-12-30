<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Ustadz;
use App\Models\Kelompok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UstadzController extends Controller
{
    public function index()
    {
        $ustadzs = Ustadz::all();
        $kelompoks = Kelompok::all();
        return view('ustadzs.index',compact('ustadzs','kelompoks'));
    }

    public function store(Request $request)
    {
         // 1. Validasi input
         $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'kelompok_id'   => 'required','integer',
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
    
               
                $user->assignRole('ustadz');
    
                // 4. Simpan ke tabel Ustadz
                Ustadz::create([
                    'user_id'       => $user->id,
                    'kelompok_id'   => $validated['kelompok_id'],
                    'kelamin'       => $validated['kelamin'],
                ]);
            });
    
            // 5. Redirect dengan flash message (dipakai SweetAlert2 di view)
            return redirect()
                ->route('ustadzs')
                ->with('success', 'Data Ustadz berhasil disimpan!');
    
        } catch (\Exception $e) {
            return redirect()
                ->route('ustadzs')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function edit(Ustadz $ustadz)
    {
        $ustadzview = Ustadz::all();
        $kelompoks = Kelompok::all();
        return  view('ustadzs.edit',compact('ustadzview','ustadz','kelompoks'));
    }

    public function update(Request $request, Ustadz $ustadz)
    {
        // Validasi input
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'kelompok_id'   => 'required|integer',
            'kelamin'       => 'required|in:laki-laki,perempuan',
            'email'         => 'required|email|unique:users,email,' . $ustadz->user_id,
            'password'      => 'nullable|string|min:6', // Password opsional
            'avatar'        => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048', // Avatar opsional
        ]);

        try {
            DB::transaction(function () use ($validated, $ustadz, $request) {

                // Tentukan avatar: jika tidak ada avatar yang diunggah, set ke default
                $avatarPath = $ustadz->user->avatar ?? 'avatar/default-avatar.png';

                // Jika ada avatar yang diunggah, simpan gambar tersebut
                if ($request->hasFile('avatar')) {
                    $avatarPath = $request->file('avatar')->store('avatars', 'public');
                }

                // 2. Update data user
                $ustadz->user->update([
                    'name'      => $validated['name'],
                    'email'     => $validated['email'],
                    'avatar'    => $avatarPath,
                    'password'  => $validated['password'] ? Hash::make($validated['password']) : $ustadz->user->password, // Jika password kosong, gunakan password lama
                ]);

                // 3. Update data ustadz
                $ustadz->update([
                    'kelompok_id'   => $validated['kelompok_id'],
                    'kelamin'       => $validated['kelamin'],
                ]);
            });

            // Redirect dengan flash message
            return redirect()->route('ustadzs')
                ->with('success', 'Data Ustadz berhasil diperbarui!');

        } catch (\Exception $e) {
            return redirect()->route('ustadzs')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }


    public function show($id)
    {
        // Ambil data Ustadz berdasarkan ID
        $ustadz = Ustadz::with('user', 'kelompok')->findOrFail($id);

        // Mengirim data dalam bentuk JSON
        return response()->json([
            'name' => $ustadz->user->name,
            'kelompok' => $ustadz->kelompok->nama_kelompok,
            'kelamin' => $ustadz->kelamin,
            'email' => $ustadz->user->email,
            'avatar' => asset('storage/' . ($ustadz->user->avatar ?: 'avatar/default-avatar.png'))
        ]);
    }


    public function destroy($id)
    {
        try {
            // Temukan data Ustadz berdasarkan ID
            $ustadz = Ustadz::with('user')->findOrFail($id);

            // Hapus avatar jika ada (Opsional)
            if ($ustadz->user->avatar && file_exists(public_path('storage/' . $ustadz->user->avatar))) {
                unlink(public_path('storage/' . $ustadz->user->avatar)); // Menghapus file avatar
            }

            // Hapus data Ustadz
            $ustadz->delete();

            // Hapus data User terkait
            $ustadz->user->delete();

            // Redirect dengan flash message sukses
            return redirect()->route('ustadzs')
                ->with('success', 'Data Ustadz berhasil dihapus!');
        } catch (\Exception $e) {
            // Jika terjadi kesalahan
            return redirect()->route('ustadzs')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

}
