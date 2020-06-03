<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
class FrontController extends Controller
{
    public function test(){
        $ujian_id = '24438fb8-8c8b-4194-a6cc-8bd3b40c8bd9';
        $ujian_id = 'faeea9d5-a0fc-4fe2-9053-55c19a06fdb4';
        $ujian_id = 'bfb6e584-73f4-4d14-ac77-2541aa9c5052';
        $ujian_id = '7731b925-aeac-4a46-b9fb-e603d37dfb18';
        $soal = Question::where('exam_id', $ujian_id)->get();
        dd($ujian);
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
