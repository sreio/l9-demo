<?php

namespace App\Providers;

use App\Models\Sanctum\PersonalAccessToken;
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


    }
}
