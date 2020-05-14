<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User_exam;
use Auth;
class HomeController extends Controller
{
    public function index(Request $request){
        return view('home');
    }
    public function reset_ujian(Request $request){
        User_exam::whereNotNull('exam_id')->delete();
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
