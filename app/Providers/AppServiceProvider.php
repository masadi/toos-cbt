<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use App\Setting;
use Carbon\Carbon;
use Ramsey\Uuid\Uuid;
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
        /*DB::listen(function ($query) {
            File::append(
                storage_path('/logs/query.log'),
                $query->sql . ' [' . implode(', ', $query->bindings) . ']' . PHP_EOL
           );
        });*/
        if(DB::connection()->getDatabaseName()){
            if (Schema::hasTable('settings')) {
                config([
                    'global' => Setting::all([
                        'key','value'
                    ])->keyBy('key') // key every setting by its name
                    ->transform(function ($setting) {
                        return $setting->value; // return only the value
                    })
                    ->toArray()
                ]);
            }
        }
        setlocale(LC_TIME, 'id_ID.utf8');
        Carbon::setLocale(LC_TIME, 'id_ID.utf8');
        Validator::extend('uuid', function ($attribute, $value, $parameters, $validator) {
            return Uuid::isValid($value);
        });
    }
}
