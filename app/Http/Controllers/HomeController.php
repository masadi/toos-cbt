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
        $user = auth()->user();
        /*$data = DB::table('sessions')->where('user_id', $user->user_id)->get();
        if($data){
            DB::table('sessions')->where('user_id', $user->user_id)->delete();
        }*/
        if($user->hasRole('sekolah')){
            $sekolah = Sekolah::where(function($query) use ($user){
                if($user){
                    if($user->sekolah_id){
                        $query->where('sekolah_id', $user->sekolah_id);
                    }
                }
            })->count();
            $ptk = Ptk::where(function($query) use ($user){
                if($user){
                    if($user->sekolah_id){
                        $query->where('sekolah_id', $user->sekolah_id);
                    }
                }
            })->count();
            $peserta_didik = Peserta_didik::where(function($query) use ($user){
                if($user){
                    if($user->sekolah_id){
                        $query->where('sekolah_id', $user->sekolah_id);
                    }
                }
            })->count();
            $bank_soal = Bank_soal::where(function($query) use ($user){
                if($user){
                    if($user->sekolah_id){
                        $query->whereHas('ptk', function($sq) use ($user) {
                            $sq->where('sekolah_id', $user->sekolah_id);
                        });
                    }
                }
            })->count();
            $rombongan_belajar = Rombongan_belajar::where(function($query) use ($user){
                if($user){
                    if($user->sekolah_id){
                        $query->where('sekolah_id', $user->sekolah_id);
                    }
                }
            })->count();
            return view('dashboard.homepage.sekolah', compact('user', 'sekolah', 'ptk', 'peserta_didik', 'bank_soal', 'rombongan_belajar'));
        } elseif($user->hasRole('proktor')){
            $find_server = Server::where('id_server', $user->username)->first();
            if($find_server && !$find_server->status){
                $find_server->status = 1;
                $find_server->save();
            }
            return view('dashboard.homepage.proktor', compact('user', 'sekolah', 'ptk', 'peserta_didik', 'bank_soal', 'rombongan_belajar'));
        } elseif($user->hasRole('peserta_didik') || $user->hasRole('ptk')){
            if($user->logout){
                $user->logout = FALSE;
                $user->save();
            }
            return view('dashboard.homepage.peserta_ujian', compact('user', 'sekolah', 'ptk', 'peserta_didik', 'bank_soal', 'rombongan_belajar'));
        } else {
            return view('dashboard.homepage.no_akses');
        }
    }
    public function signout(){
        $user = Auth::user();
        if($user->hasRole('proktor')){
            $redirect = 'login-proktor';
        } elseif($user->hasRole('peserta_didik')){
            $redirect = 'login';
        } else {
            $redirect = 'login-sekolah';
        }
        $redirect = 'login';
        $user->logout = TRUE;
        $user->save();
        Auth::logout();
        //return route('login');
        return redirect(url($redirect));
    }
}
