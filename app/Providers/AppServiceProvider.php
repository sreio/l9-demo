<?php

namespace App\Providers;

use App\Models\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;

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
        Schema::defaultStringLength(191);

        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class); // set sanctum model

        // 定义sql宏
        \Illuminate\Database\Query\Builder::macro('sql', function () {
            return array_reduce($this->getBindings(), function ($sql, $binding) {
                return preg_replace('/\?/', is_numeric($binding) ? $binding : "'" . $binding . "'", $sql, 1);
            }, $this->toSql());
        });
        // Eloquent ORM
        \Illuminate\Database\Eloquent\Builder::macro('sql', function () {
            return ($this->getQuery()->sql());
        });

        if (config('app.listen_db')) {
            DB::listen(function ($query){
                $sql = array_reduce($query->bindings, function ($sql, $binding) {
                    return preg_replace('/\?/', is_numeric($binding) ? $binding : "'" . $binding . "'", $sql, 1);
                }, $query->sql);

                $location = collect(debug_backtrace())->filter(function ($trace) {
                    if (!isset($trace['file'])) return false;
                    return !str_contains($trace['file'], 'vendor/');
                })->first(); // grab the first element of non vendor/ calls

                if (config('app.listen_slow_db') && $query->time >= config('app.listen_slow_db_time')) {
                    Log::channel('slow_db')->warning("\n------------\nSql: $sql \nTime: $query->time \nFile: {$location['file']} \nLine: ${location['line']}\n------------");
                }

                log::channel('db')->info("[{$query->time}] $sql");
            });
        }

    }
}
