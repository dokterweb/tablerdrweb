<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
 
    // Dashboard untuk Admin
    public function indexadmin()
    {
        return view('dashboard.admin');  // Halaman dashboard admin
    }

    // Dashboard untuk Ustadz
    public function indexustadz()
    {
        return view('dashboard.ustadz');  // Halaman dashboard ustadz
    }

    // Dashboard untuk Siswa
    public function indexsiswa()
    {
        return view('dashboard.siswa');  // Halaman dashboard siswa
    }
}
