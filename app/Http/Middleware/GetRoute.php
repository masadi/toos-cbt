<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Event;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use MobileDetect;
class GetRoute
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $name = Route::currentRouteName();
        $name = explode('.', $name);
        if(is_array($name) && $name[0] == 'ujian'){
            config(['adminlte.layout_topnav' => true]);
            config(['adminlte.classes_topnav' => 'navbar-dark navbar-primary py-4']);
            config(['adminlte.logo_img' => NULL]);
            config(['adminlte.logo_img_class' => NULL]);
            config(['adminlte.logo_img_alt' => NULL]);
            config(['adminlte.logo' => '<b class="name_text">CBT TOOS V.3.x</b>']);
            config(['adminlte.classes_body' => '']);
            config(['adminlte.classes_navbar' => 'mx-auto']);
            config(['adminlte.right_sidebar' => TRUE]);
        }
        if (Auth::check()){
            config(['app.timezone' => Auth::user()->timezone]);
            foreach(MobileDetect::getProperties() as $browser => $match){
                $check = MobileDetect::version($browser);
                if($check!==false){
                    if($browser == 'Firefox'){
                        if(version_compare($check, '76.0', '<')){
                            return redirect()->route('update_browser', ['name' => $browser]);
                        }
                    } elseif($browser == 'Chrome'){
                        if(version_compare($check, '81.0', '<')){
                            return redirect()->route('update_browser', ['name' => $browser]);
                        }
                    }
                }
            }
            if(Auth::user()->hasRole('peserta_didik') && Auth::user()->logout){
                Auth::logout();
                return redirect('login')->with('success', 'Sesi login anda di reset oleh proktor. Silahkan login kembali');
            }
            View::share('user', Auth::user());
        }
        Event::listen(BuildingMenu::class, function (BuildingMenu $event) use ($name){
            if($name[0] == 'ujian'){
                $event->menu->add([]);
            } else {
                $event->menu->add([
                    'text' => 'sekolah',
                    'search' => false,
                    'topnav' => true,
                ]);
                $event->menu->add([
                    'text' => 'Dashboard',
                    'url' => 'home',
                    'icon' => 'fas fa-fw fa-tachometer-alt',
                    'active' => ['/']
                ]);
                $event->menu->add([
                    'text' => 'Status Download',
                    'url'  => 'status-download',
                    'permission'  => 'proktor',
                    'icon' => 'fas fa-fw fa-cloud-download-alt',
                ]);
                $event->menu->add([
                    'text' => 'Jadwal Ujian',
                    'url'  => 'jadwal-ujian',
                    'permission'  => 'proktor',
                    'icon' => 'fas fa-fw fa-calendar',
                ]);
                $event->menu->add([
                    'text' => 'Daftar Peserta Didik',
                    'url'  => 'daftar-peserta',
                    'permission'  => 'proktor',
                    'icon' => 'fas fa-fw fa-users',
                ]);
                /*$event->menu->add([
                    'text' => 'Daftar PTK',
                    'url'  => 'daftar-ptk',
                    'permission'  => 'proktor',
                    'icon' => 'fas fa-fw fa-user-plus',
                ]);
                $event->menu->add([
                    'text' => 'Status Tes',
                    'url'  => 'status-test',
                    'permission'  => 'proktor',
                    'icon' => 'fas fa-fw fa-pencil-ruler',
                ]);*/
                $event->menu->add([
                    'text' => 'Status Peserta',
                    'url'  => 'status-peserta',
                    'permission'  => 'proktor',
                    'icon' => 'fas fa-fw fa-user-clock',
                ]);
                $event->menu->add([
                    'text' => 'Reset Login',
                    'url'  => 'reset-login',
                    'permission'  => 'proktor',
                    'icon' => 'fas fa-fw fa-sync-alt',
                ]);
                $event->menu->add([
                    'text' => 'Hapus Data',
                    'url'  => 'hapus-data',
                    'permission'  => 'proktor-salah',
                    'icon' => 'fas fa-fw fa-trash',
                ]);
                $event->menu->add([
                    'text' => 'Proses Ujian',
                    'url'  => 'ujian/token',
                    'permission'  => 'peserta_didik-salah',
                    'icon' => 'fas fa-fw fa-sync-alt',
                ]);
                $event->menu->add([
                    'text' => 'Hasil Ujian',
                    'url'  => 'ujian/hasil',
                    'permission'  => 'peserta_didik-salah',
                    'icon' => 'fas fa-fw fa-sync-alt',
                ]);
                $event->menu->add([
                    'text' => 'Logout',
                    'url'  => 'logout',
                    'icon' => 'fas fa-fw fa-sign-out-alt',
                    'icon_color' => 'red',
                ]);
            }
        });
        return $next($request);
    }
}
