<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
class FrontController extends Controller
{
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
