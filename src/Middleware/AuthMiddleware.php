<?php

namespace DeSmart\JWTAuth\Middleware;

use Illuminate\Http\Request;
use Lcobucci\JWT\Token;
use DeSmart\JWTAuth\Auth\Guard;
use DeSmart\JWTAuth\Exception\ExpiredTokenException;
use DeSmart\JWTAuth\Jwt\TokenFactory;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * Class AuthMiddleware
 *
 * Tries to log in user based on JWT.
 * If this fails proper exception will be thrown.
 */
class AuthMiddleware
{

    /**
     * @var Guard
     */
    protected $guard;

    /**
     * @var TokenFactory
     */
    protected $tokenFactory;

    public function __construct(Guard $guard, TokenFactory $tokenFactory)
    {
        $this->guard = $guard;
        $this->tokenFactory = $tokenFactory;
    }

    /**
     * @param Request $request
     * @param callable $next
     * @return mixed
     * @throws UnauthorizedHttpException
     */
    public function handle(Request $request, callable $next)
    {
        $token = $this->tokenFactory->createFromRequest($request);
        $this->validateToken($token);

        $this->guard->loginByToken($token);

        if (false === $this->guard->isUserLogged()) {
            $this->throwUnauthorizedException();
        }

        return $next($request);
    }

    /**
     * Validates JWT token
     *
     * @param Token $token
     * @throws UnauthorizedHttpException when token has expired or is invalid
     */
    protected function validateToken(Token $token = null)
    {
        if (null === $token) {
            $this->throwUnauthorizedException();
        }

        if (time() < $token->getClaim('exp')) {
            return;
        }

        $this->throwUnauthorizedException();
    }

    protected function throwUnauthorizedException()
    {
        throw new UnauthorizedHttpException('Bearer');
    }
}
