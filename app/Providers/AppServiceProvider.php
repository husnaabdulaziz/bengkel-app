<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Company;
use App\Observers\CompanyObserver;
use App\Models\StockMovement;
use App\Observers\StockMovementObserver;
use Illuminate\Auth\Events\Login;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Event;

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
        \Illuminate\Support\Facades\Gate::before(function ($user, $ability) {
            return $user->is_super_admin ? true : null;
        });
        
        Company::observe(CompanyObserver::class);
        StockMovement::observe(StockMovementObserver::class);

        Event::listen(Login::class, function (Login $event) {
            ActivityLog::create([
                'company_id' => $event->user->company_id,
                'user_id' => $event->user->id,
                'action' => 'login',
                'description' => $event->user->name . ' login ke sistem',
                'ip_address' => request()->ip(),
            ]);
        });
    }
}