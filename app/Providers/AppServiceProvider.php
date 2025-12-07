<?php

namespace App\Providers;

use App\Repositories\setting\settingRepositoryInterface;
use App\Repositories\setting\settingRepository;

use App\Repositories\Company\CompanyRepositoryInterface;
use App\Repositories\Company\CompanyRepository;

use App\Repositories\notifications\notificationsRepositoryInterface;
use App\Repositories\notifications\notificationsRepository;

use App\Repositories\withdraw\withdrawRepositoryInterface;
use App\Repositories\withdraw\withdrawRepository;

use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\User\UserRepository;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void {
//
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(withdrawRepositoryInterface::class, withdrawRepository::class);
        $this->app->bind(notificationsRepositoryInterface::class, notificationsRepository::class);
        $this->app->bind(CompanyRepositoryInterface::class, CompanyRepository::class);
        $this->app->bind(settingRepositoryInterface::class, settingRepository::class);
}

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
