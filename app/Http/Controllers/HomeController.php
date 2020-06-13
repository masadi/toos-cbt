<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Exam;
use App\User_exam;
use Auth;
class HomeController extends Controller
{
    public function index(Request $request){
        //Cache::forget('get_soal');
        //Cache::forget('get_ujian');
        //Cache::forget('ujian');
        $user = auth()->user();
        $exams = [];
        if($user->hasRole('peserta_didik')){
            $anggota = $user->anggota_rombel()->where('semester_id', config('global.semester_id'))->first();
            if($anggota){
                $exams = Exam::withCount('question')->with(['jadwal', 'user_exam' => function($query) use ($user){
                    $query->where('user_id', $user->user_id);
                }])->where(function($query) use ($anggota){
                    $query->whereIn('pembelajaran_id', function($query) use ($anggota){
                        $query->select('pembelajaran_id')->from('pembelajaran')->where('rombongan_belajar_id', $anggota->rombongan_belajar_id);
                    });
                    $query->whereHas('jadwal');
                })->get();
            }
        }
        return view('home', compact('exams'));
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
