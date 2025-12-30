<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UstadzController;
use App\Http\Controllers\KelasnyaController;
use App\Http\Controllers\KelompokController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('login', [AuthController::class, 'index'])->name('login');
Route::post('login', [AuthController::class, 'handleLogin']);
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

// Route untuk Admin
Route::middleware(['auth', 'role:admin'])->get('/admin/dashboard', [DashboardController::class, 'indexadmin'])->name('admin.dashboard');

// Route untuk Ustadz
Route::middleware(['auth', 'role:ustadz'])->get('/ustadz/dashboard', [DashboardController::class, 'indexustadz'])->name('ustadz.dashboard');

// Route untuk Siswa
Route::middleware(['auth', 'role:siswa'])->get('/siswa/dashboard', [DashboardController::class, 'indexsiswa'])->name('siswa.dashboard');

Route::get('/kelasnyas',[KelasnyaController::class, 'index'])->middleware('role:admin')->name('kelasnyas');
Route::post('kelasnyas/store', [KelasnyaController::class, 'store'])->middleware('role:admin')->name('kelasnyas.store');
Route::get('kelasnyas/{kelasnya}/edit', [KelasnyaController::class, 'edit'])->middleware('role:admin')->name('kelasnyas.edit');
Route::put('kelasnyas/{kelasnya}', [KelasnyaController::class, 'update'])->middleware('role:admin')->name('kelasnyas.update');
Route::delete('kelasnyas/{kelasnya}', [KelasnyaController::class, 'destroy'])->middleware('role:admin')->name('kelasnyas.destroy');

Route::get('/kelompoks',[KelompokController::class, 'index'])->middleware('role:admin')->name('kelompoks');
Route::post('kelompoks/store', [KelompokController::class, 'store'])->middleware('role:admin')->name('kelompoks.store');
Route::get('kelompoks/{kelompok}/edit', [KelompokController::class, 'edit'])->middleware('role:admin')->name('kelompoks.edit');
Route::put('kelompoks/{kelompok}', [KelompokController::class, 'update'])->middleware('role:admin')->name('kelompoks.update');
Route::delete('kelompoks/{kelasnya}', [KelompokController::class, 'destroy'])->middleware('role:admin')->name('kelompoks.destroy');

Route::get('/ustadzs',[UstadzController::class, 'index'])->middleware('role:admin')->name('ustadzs');
Route::post('ustadzs/store', [UstadzController::class, 'store'])->middleware('role:admin')->name('ustadzs.store');
Route::get('ustadzs/{ustadz}/edit', [UstadzController::class, 'edit'])->middleware('role:admin')->name('ustadzs.edit');
Route::put('ustadzs/{ustadz}', [UstadzController::class, 'update'])->middleware('role:admin')->name('ustadzs.update');
Route::get('ustadzs/{ustadz}', [UstadzController::class, 'show'])->middleware('role:admin')->name('ustadzs.show');
Route::delete('ustadzs/{ustadz}', [UstadzController::class, 'destroy'])->middleware('role:admin')->name('ustadzs.destroy');