<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\User_exam;
use Auth;
class HomeController extends Controller
{
    public function index(Request $request){
        Cache::forget('get_soal');
        Cache::forget('get_ujian');
        Cache::forget('ujian');
        return view('home');
    }
    public function reset_ujian(Request $request){
        User_exam::whereNotNull('exam_id')->delete();
        Exam::whereAktif(1)->update(['aktif' => 0]);
        echo 'reset_ujian';
    }
    public function logout(){
        $user = auth()->user();
        $user->logout = TRUE;
        $user->save();
        Auth::logout();
        return redirect('login')->with('success', 'Logout berhasil. Terima kasih');
    }
}
