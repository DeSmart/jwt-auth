<?php

namespace DeSmart\JWTAuth\Middleware;

use DeSmart\JWTAuth\Auth\Guard;
use DeSmart\JWTAuth\Jwt\TokenFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TokenRefreshMiddleware
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

    public function handle(Request $request, callable $next): Response
    {
        $this->beforeRefresh();

        $response = $next($request);

        if (true === $this->guard->isUserLogged()) {
            $token = $this->tokenFactory->createForUser($this->guard->getUser());
            $response->headers->set('Authorization', 'Bearer '.$token->__toString());
            $this->afterRefresh();
        }

        return $response;
    }

    /**
     * Code executed before logg-in attempt.
     *
     * Usable by extending this middleware
     */
    protected function beforeRefresh()
    {
    }

    /**
     * Code executed after successful logging in.
     *
     * Usable by extending this middleware
     */
    protected function afterRefresh()
    {
    }
}
