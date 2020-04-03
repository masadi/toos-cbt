<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Setting;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use Helper;
//use Illuminate\Pagination\Paginator;
//use Illuminate\Pagination\LengthAwarePaginator;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (Schema::hasTable('settings')) {
            config([
                'global' => Setting::all([
                    'key','value'
                ])->whereNull('sekolah_id')
                ->keyBy('key') // key every setting by its name
                ->transform(function ($setting) {
                    return $setting->value; // return only the value
                })
                ->toArray(),
                'internet' => Helper::internet(),
            ]);
        };
        config(['self-update.version_installed' => config('global.app_version')]);
        Schema::defaultStringLength(191);
        Carbon::setLocale($this->app->getLocale());
        //LengthAwarePaginator::defaultView('vendor.pagination.ujian');
        //LengthAwarePaginator::defaultSimpleView('vendor.pagination.ujian');
        //$a = \Carbon\Carbon::now()->format('l, d F Y H:i');
        //dd($a);
    }
}
