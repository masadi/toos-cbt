<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Auth;
class UserEventSubscriber
{
    /**
     * Handle user login events.
     */
    public function handleUserLogin($event) {
        if($event->user->hasRole('peserta_didik')){
            if(!$event->user->logout){
                dd($event);
                Auth::logout();
                return redirect()->route('login')->with('error', 'Pengguna sedang aktif. Silahkan hubungi Proktor');
            }
        }
    }

    /**
     * Handle user logout events.
     */
    public function handleUserLogout($event) {
        if($event->user->hasRole('peserta_didik')){
            $event->user->logout = TRUE;
            $event->user->save();
        }
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  \Illuminate\Events\Dispatcher  $events
     */
    public function subscribe($events)
    {
        $events->listen(
            'Illuminate\Auth\Events\Login',
            'App\Listeners\UserEventSubscriber@handleUserLogin'
        );

        $events->listen(
            'Illuminate\Auth\Events\Logout',
            'App\Listeners\UserEventSubscriber@handleUserLogout'
        );
    }
}
