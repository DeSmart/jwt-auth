# Laravel JWT Auth
Simple JWT auth implementation for Laravel.

---

### Installation
Install package using Composer:
```
composer require desmart/jwt-auth
```

Register the package's service provider in `config/app.php`:
```
'providers' => [
        (...)
        DeSmart\JWTAuth\ServiceProvider::class,
    ],
```

### Configuration
The package comes with a config file. In order to publish it, run the following command:
```
php artisan vendor:publish
```

The config file allows you to change some options. Be sure to check it out.

### Usage
The package allows to:
1. Authenticate a user,
2. Verify if the user is authenticated using route middleware.

### User authentication
First, add the `TokenRefreshMiddleware` as a global middleware or to a middleware group (`app/Http/Kernel.php`). It will add the `Authorization` header to the response. This header will contain the JWT token, after successful authentication.

Then, inject `\DeSmart\JWTAuth\Auth\Guard` into your auth class and authenticate the user (credentials validation is up to you).
```
public function authenticateUser(\DeSmart\JWTAuth\Auth\Guard $auth, $user) {
    if (true === $this->validateCredentails($user)) {
        $auth->loginUser($user);
    }
}
```

### Token verification
Once a user has been authenticated, each request to your application should contain the `Authorization` header with the token obtained after succesful authentication.

Add `AuthMiddleware` to the your `$routeMiddleware` array (`app/Http/Kernel.php`):
```
protected $routeMiddleware = [
        (...)
        'auth' => \DeSmart\JWTAuth\Middleware\AuthMiddleware::class,
    ];
```

Now, simply use the `auth` route middleware to check if a user is authenticated.
