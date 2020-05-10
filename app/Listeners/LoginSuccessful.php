<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Session;
use Auth;
class LoginSuccessful
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  Login  $event
     * @return void
     */
    public function handle(Login $event)
    {
        if($event->user->hasRole('peserta_didik')){
            //if(!$event->user->isActive()){
                //Auth::logout();
                //return redirect('login')->with('error', 'Pengguna sedang aktif. Silahkan hubungi Proktor');
                //dd($event);
                //Auth::logout();
                //return redirect('login')->with('error', 'Pengguna sedang aktif. Silahkan hubungi Proktor');
            //}
            $user = $event->user;
            $user->logout = FALSE;
            $user->save();
            Session::flash('login-success', 'Hello ' . $event->user->name . ', welcome back!');
        }
    }
}
