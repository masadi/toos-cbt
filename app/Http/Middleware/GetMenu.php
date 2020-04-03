<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Http\Menus\GetSidebarMenu;
use App\Models\Menulist;
use App\Models\RoleHierarchy;
use Spatie\Permission\Models\Role;
use App\Setting;
use Helper;
use Illuminate\Support\Facades\Schema;
class GetMenu
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
        if (Auth::check()){
            $role = 'guest';
            //$role =  Auth::user()->menuroles;
            $userRoles = Auth::user()->getRoleNames();
            //$userRoles = $userRoles['items'];
            $roleHierarchy = RoleHierarchy::select('role_hierarchy.role_id', 'roles.name')
            ->join('roles', 'roles.id', '=', 'role_hierarchy.role_id')
            ->orderBy('role_hierarchy.hierarchy', 'asc')->get();
            $flag = false;
            foreach($roleHierarchy as $roleHier){
                foreach($userRoles as $userRole){
                    if($userRole == $roleHier['name']){
                        $role = $userRole;
                        $flag = true;
                        break;
                    }
                }
                if($flag === true){
                    break;
                }
            }
            config(['app.timezone' => Auth::user()->timezone]);
            if (Schema::hasTable('settings')) {
                config([
                    'local' => Setting::all([
                        'key','value'
                    ])->where('sekolah_id', Auth::user()->sekolah_id)
                    ->keyBy('key') // key every setting by its name
                    ->transform(function ($setting) {
                        return $setting->value; // return only the value
                    })
                    ->toArray()
                ]);
            };
        }else{
            $role = 'guest';
        }
        //session(['prime_user_role' => $role]);
        $menus = new GetSidebarMenu();
        $menulists = Menulist::all();
        $result = array();
        foreach($menulists as $menulist){
            $result[ $menulist->name ] = $menus->get( $role, $menulist->id );
        }
        view()->share('appMenus', $result );
        return $next($request);
    }
}