<?php

namespace App\Providers;

use App\Models\Attendee;
use App\Models\Event;
use App\Models\User;
use Illuminate\Auth\Access\Gate;
use Illuminate\Contracts\Auth\Access\Gate as AccessGate;
use Illuminate\Support\Facades\Gate as FacadesGate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        FacadesGate::define('update-event', function (User $user, Event $event) {
            return $user->id === $event->user_id;
        });


        FacadesGate::define('delete-attendee', function (User $user, Event $event, Attendee $attendee) {
            return $user->id === $event->user_id || $user->id === $attendee->user_id;
        });
    }
}
