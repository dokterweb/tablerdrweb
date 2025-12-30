<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AuthController extends Controller
{
     // Menampilkan Form Login
     public function index()
     {
         return view('auth.login');
     }
 
     public function handleLogin(Request $request)
     {
         $credential = $request->validate([
             'email' => 'required|email|exists:users,email',
             'password' => 'required|string|min:8', // Set password minimum 8 karakter
         ],[
             'email.required'    => 'Email harus di isi',
             'email.email'       => 'Email tidak valid',
             'password.required' => 'Password harus di isi',
             'password.min'      => 'Password harus memiliki minimal 8 karakter',
         ]);
         
 
         if (Auth::attempt($credential)) {
             // dd('berhasil login');
             $request->session()->regenerate();
             $user = Auth::user();
             if ($user->hasRole('admin')) {
                 return redirect()->route('admin.dashboard');
             } else if ($user->hasRole('ustadz')) {
                return redirect()->route('ustadz.dashboard');
             } else {
                 return redirect()->route('siswa.dashboard');
             }
         }
         return back()->withErrors([
             'email'     => 'Tidak sesuai dengan database',
         ])->onlyInput('email');
         
     }
 
     public function logout(Request $request)
     {
         Auth::logout();
         $request->session()->invalidate();
         $request->session()->regenerateToken();
         return redirect('/');
     }
}
