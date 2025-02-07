<?php
namespace App\Providers;

use App\Services\CompanyService;
use Illuminate\Support\ServiceProvider;
use App\Contracts\Repositories\UserRepositoryInterface;
use App\Repositories\UserRepository;
use App\Contracts\Services\UserServiceInterface;
use App\Services\UserService;
use App\Contracts\Services\AuthServiceInterface;
use App\Contracts\Services\CompanyServiceInterface;
use App\Contracts\Repositories\CompanyRepositoryInterface;
use App\Contracts\Services\FileServiceInterface;
use App\Services\FileService;
use App\Repositories\FileRepository;
use App\Contracts\Repositories\FileRepositoryInterface;
use App\Repositories\CompanyRepository;
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

        $this->app->bind(CompanyServiceInterface::class, function ($app) {
            return new CompanyService($app->make(CompanyRepositoryInterface::class));
        });

        $this->app->bind(CompanyRepositoryInterface::class, CompanyRepository::class);

        $this->app->bind(FileServiceInterface::class, function ($app) {
            return new FileService($app->make(FileRepositoryInterface::class), $app->make(CompanyServiceInterface::class));
        });

        $this->app->bind(FileRepositoryInterface::class, FileRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
