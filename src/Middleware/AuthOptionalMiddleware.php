<?php

namespace DeSmart\JWTAuth\Middleware;

use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * Class AuthMiddleware
 *
 * Tries to log in user based on JWT.
 * If this fails proper exception will be thrown.
 */
class AuthOptionalMiddleware extends AuthMiddleware
{
    /**
     * @param Request $request
     * @param callable $next
     * @return mixed
     * @throws UnauthorizedHttpException
     */
    public function handle(Request $request, callable $next)
    {
        $token = $this->tokenFactory->createFromRequest($request);

        if (null === $token) {
            return $next($request);
        }

        $this->validateToken($token);

        $this->guard->loginByToken($token);

        if (false === $this->guard->isUserLogged()) {
            $this->throwUnauthorizedException();
        }

        return $next($request);
    }
}
