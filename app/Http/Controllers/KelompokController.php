<?php

namespace App\Http\Controllers;

use App\Models\Kelompok;
use Illuminate\Http\Request;

class KelompokController extends Controller
{
    public function index()
    {
        $kelompoks = Kelompok::all();
        return view('kelompoks.index',compact('kelompoks'));
    }

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'nama_kelompok' => 'required|string|max:255'
        ]);
        try {
            // Simpan data ke database
            Kelompok::create([
                'nama_kelompok' => $request->nama_kelompok, // Sesuaikan dengan nama kolom di database
            ]);
            // Notifikasi sukses
            return redirect()->route('kelompoks')
                             ->with('success', 'Data berhasil ditambahkan.');
        } catch (\Exception $e) {
            // Notifikasi error jika terjadi kesalahan
            return redirect()->route('kelompoks')
                             ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function edit(Kelompok $kelompok)
    {
        $kelompokview = Kelompok::all();
        // dd($kelompok);
        return view('kelompoks.edit',compact('kelompok','kelompokview'));
    }

    public function update(Request $request, Kelompok $kelompok)
    {
        // Validasi input
        $request->validate(['nama_kelompok' => 'required|string|max:255']);

        try {
            // Update data KelasSub
            $kelompok->update([
                'nama_kelompok' => $request->nama_kelompok, 
            ]);

            // Notifikasi sukses
            return redirect()->route('kelompoks')
                            ->with('success', 'Data berhasil diperbarui.');
        } catch (\Exception $e) {
            // Notifikasi error jika terjadi kesalahan
            return redirect()->route('kelompoks')
                            ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            // Temukan kelas berdasarkan ID
            $kelompok = Kelompok::findOrFail($id);
            
            // Hapus data
            $kelompok->delete();

            // Redirect dengan notifikasi sukses
            return redirect()->route('kelompoks')
                            ->with('success', 'Data kelas berhasil dihapus.');
        } catch (\Exception $e) {
            // Jika terjadi kesalahan
            return redirect()->route('kelompoks')
                            ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
