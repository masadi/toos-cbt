<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Exam;
use App\User;
use Storage;
class FrontController extends Controller
{
    public function test(){
        $password = '12345678';
        $all_user = User::whereRoleIs('peserta_didik')->get();
        foreach($all_user as $user){
            $user->password = app('hash')->make($password);
            $user->default_password = $password;
            $user->save();
        }
        dd($all_user);
        $all_ujian = Exam::with('pembelajaran')->whereAktif(1)->get();
        foreach($all_ujian as $exam){
            $all_user = User::whereHas('peserta_didik', function($query) use ($exam){
                $query->whereHas('anggota_rombel', function($query) use ($exam){
                    $query->where('rombongan_belajar_id', $exam->pembelajaran->rombongan_belajar_id);
                });
            })->get();
            if($all_user->count()){
                foreach($all_user as $user){
                    $json_file_ujian = 'ujian-'.$user->user_id.'-'.$exam->exam_id.'.json';
                    $get_ujian = Exam::withCount(['question', 'user_question' => function($query) use ($user){
                        $query->where('user_questions.user_id', $user->user_id);
                    }])->with(['question' => function($query){
                        $query->with('answers');
                        $query->orderBy('soal_ke');
                    }, 'user_exam' => function($query) use ($user){
                        $query->where('user_exams.user_id', $user->user_id);
                    }])->find($exam->exam_id);
                    Storage::disk('public')->put($json_file_ujian, $get_ujian->toJson());
                    $json_file_all = 'all-'.$user->user_id.'-'.$exam->exam_id.'.json';
                    $collection = collect($get_ujian->question);
                    $shuffled = $collection->shuffle();
                    $first = $shuffled->first();
                    $all = $shuffled->all();
                    Storage::disk('public')->put($json_file_all, $shuffled->toJson());
                }
            }
        }
    }
    public function logout(){
        $user = auth()->user();
        if($user){
            $user->logout = TRUE;
            $user->save();
            Auth::logout();
        }
        return redirect('login')->with('success', 'Logout berhasil. Terima kasih');
    }
}
