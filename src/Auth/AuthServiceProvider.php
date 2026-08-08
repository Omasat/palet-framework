<?php

declare(strict_types=1);

namespace Palet\Framework\Auth;

use Palet\Framework\Foundation\Providers\ServiceProvider;
use Palet\Framework\Contracts\Auth\PasswordHasherInterface;
use Palet\Framework\Contracts\Database\DatabaseManagerInterface;
use Palet\Framework\Auth\Guards\SessionGuard;
use Palet\Framework\Auth\Providers\DatabaseUserProvider;
use Palet\Framework\Contracts\Session\SessionManagerInterface;
use Palet\Framework\Contracts\Cookie\CookieJarInterface;
use Palet\Framework\Contracts\Http\RequestInterface;

class AuthServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->registerAuthenticator();
        $this->registerUserProvider();
    }

    protected function registerAuthenticator(): void
    {
        $this->app->singleton(PasswordHasherInterface::class, function ($app) {
            return new PasswordHasher();
        });

        $this->app->singleton('auth', function ($app) {
            $auth = new AuthManager();

            // Default web guard using SessionGuard
            $auth->extend('web', function () use ($app, $auth) {
                // Determine user provider
                $config = $app->make('config')->get('auth.guards.web', []);
                $providerName = $config['provider'] ?? 'users';
                
                $providerConfig = $app->make('config')->get("auth.providers.{$providerName}", []);
                $model = $providerConfig['model'] ?? \App\Models\User::class;

                $hasher = $app->make(PasswordHasherInterface::class);
                $db = $app->make(DatabaseManagerInterface::class);
                
                $provider = new DatabaseUserProvider($db, $hasher, $model);
                
                // Since SessionGuard only requires provider
                return new SessionGuard($provider);
            });

            return $auth;
        });
    }

    protected function registerUserProvider(): void
    {
        // Custom providers can be registered here
    }

    public function boot(): void
    {
        // Add middleware or other boot logic if needed
    }
}
