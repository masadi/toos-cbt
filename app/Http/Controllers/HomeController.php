<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Sekolah;
use App\Ptk;
use App\Peserta_didik;
use App\Bank_soal;
use App\Rombongan_belajar;
use App\User;
use App\Server;
use Auth;
class HomeController extends Controller
{
    public function index(Request $request){
        return view('home');
    }
    public function logout(){
        $user = auth()->user();
        $user->logout = TRUE;
        $user->save();
        Auth::logout();
        return redirect('login')->with('success', 'Logout berhasil. Terima kasih');
    }
}
