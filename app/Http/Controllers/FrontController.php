<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Exam;
class FrontController extends Controller
{
    public function test(){
        $user_id = 'b1440af9-5157-4340-8c7d-26e7b170d735';
        $ujian_id = 'faeea9d5-a0fc-4fe2-9053-55c19a06fdb4';
        $json_file_ujian = 'ujian-'.$user_id.'-'.$ujian_id.'.json';
                    //if(!Storage::disk('public')->exists($json_file_ujian)){
                        $get_ujian = Exam::withCount(['question', 'user_question' => function($query) use ($user_id){
                            $query->where('user_questions.user_id', $user_id);
                        }])->with(['question' => function($query){
                            $query->with('answers');
                            $query->orderBy('soal_ke');
                        }, 'user_exam' => function($query) use ($user_id){
                            $query->where('user_exams.user_id', $user_id);
                        }])->find($ujian_id);
                        Storage::disk('public')->put($json_file_ujian, $get_ujian->toJson());
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
