<?php

namespace DeSmart\JWTAuth;

use DeSmart\JWTAuth\Auth\Guard;
use DeSmart\JWTAuth\Jwt\TokenFactory;
use DeSmart\JWTAuth\Middleware\AuthMiddleware;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/jwt-auth.php' => config_path('jwt-auth.php'),
        ]);
    }

    public function register()
    {
        $config = $this->app['config']->get('jwt-auth');

        $this->app->singleton(Guard::class, function () use ($config) {
            return new Guard(new $config['user_model_class']);
        });

        $this->registerMiddleware();
        $this->registerTokenFactory();
    }

    protected function registerMiddleware()
    {
        $this->app->bind(AuthMiddleware::class, function () {

            return new AuthMiddleware(
                $this->app->make(Guard::class),
                $this->app->make(TokenFactory::class)
            );
        });
    }

    protected function registerTokenFactory()
    {
        $this->app->bind(TokenFactory::class, function () {
            $config = $this->app['config']->get('jwt-auth.jwt');

            return new TokenFactory(
                $config['expiration_ttl'],
                $config['secret_token']
            );
        });
    }
}
