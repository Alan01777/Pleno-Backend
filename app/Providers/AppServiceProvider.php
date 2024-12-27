<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\Repositories\UserRepositoryInterface;
use App\Repositories\UserRepository;
use App\Contracts\Services\UserServiceInterface;
use App\Services\UserService;
use App\Contracts\Services\AuthServiceInterface;
use App\Services\AuthService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserServiceInterface::class, function ($app) {
            return new UserService($app->make(UserRepositoryInterface::class));
        });

        $this->app->bind(AuthServiceInterface::class, function ($app) {
            return new AuthService($app->make(UserRepositoryInterface::class));
        });

        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
