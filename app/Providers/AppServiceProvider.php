<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
        // Compartir contador de notificaciones no leídas con todas las vistas
        view()->composer('plantilla', function ($view) {
            $unreadCount = 0;
            if (auth()->check()) {
                $unreadCount = \App\Models\UserNotification::where('user_id', auth()->id())
                    ->whereNull('read_at')
                    ->count();
            }
            $view->with('unreadNotificationsCount', $unreadCount);
        });
    }
}
