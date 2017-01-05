<?php

namespace test\JWTAuth\Middleware;

use DeSmart\JWTAuth\Auth\Guard;
use DeSmart\JWTAuth\Jwt\TokenFactory;
use DeSmart\JWTAuth\Middleware\AuthMiddleware;
use Illuminate\Http\Request;
use Lcobucci\JWT\Token;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class AuthMiddlewareTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function it_verifies_token()
    {
        $guard = $this->createMock(Guard::class);
        $factory = $this->createMock(TokenFactory::class);
        $token = $this->createMock(Token::class);
        $request = $this->createMock(Request::class);

        $factory->expects($this->once())
            ->method('createFromRequest')
            ->with($this->equalTo($request))
            ->willReturn($token);

        $token->expects($this->any())
            ->method('getClaim')
            ->with($this->equalTo('exp'))
            ->willReturn(time() + 3600);

        $guard->expects($this->once())
            ->method('loginByToken')
            ->with($this->equalTo($token));

        $guard->expects($this->any())
            ->method('isUserLogged')
            ->willReturn(true);

        $callback = function ($request) {
            return $request;
        };

        $middleware = new AuthMiddleware($guard, $factory);
        $result = $middleware->handle($request, $callback);

        $this->assertSame($request, $result);
    }

    /** @test */
    public function it_fails_when_token_is_null()
    {
        $guard = $this->createMock(Guard::class);
        $factory = $this->createMock(TokenFactory::class);
        $request = $this->createMock(Request::class);

        $factory->expects($this->once())
            ->method('createFromRequest')
            ->with($this->equalTo($request))
            ->willReturn(null);

        $callback = function ($request) {
        };

        $middleware = new AuthMiddleware($guard, $factory);

        $this->expectException(UnauthorizedHttpException::class);
        $middleware->handle($request, $callback);
    }

    /** @test */
    public function it_fails_when_token_is_expired()
    {
        $guard = $this->createMock(Guard::class);
        $factory = $this->createMock(TokenFactory::class);
        $token = $this->createMock(Token::class);
        $request = $this->createMock(Request::class);

        $factory->expects($this->once())
            ->method('createFromRequest')
            ->with($this->equalTo($request))
            ->willReturn($token);

        $token->expects($this->any())
            ->method('getClaim')
            ->with($this->equalTo('exp'))
            ->willReturn(time() - 3600);

        $guard->expects($this->never())
            ->method('loginByToken');

        $callback = function ($request) {
        };

        $middleware = new AuthMiddleware($guard, $factory);

        $this->expectException(UnauthorizedHttpException::class);
        $middleware->handle($request, $callback);
    }

    /** @test */
    public function it_fails_when_guard_fails_logging_user()
    {
        $guard = $this->createMock(Guard::class);
        $factory = $this->createMock(TokenFactory::class);
        $token = $this->createMock(Token::class);
        $request = $this->createMock(Request::class);

        $factory->expects($this->once())
            ->method('createFromRequest')
            ->with($this->equalTo($request))
            ->willReturn($token);

        $token->expects($this->any())
            ->method('getClaim')
            ->with($this->equalTo('exp'))
            ->willReturn(time() + 3600);

        $guard->expects($this->once())
            ->method('loginByToken')
            ->with($this->equalTo($token));

        $guard->expects($this->any())
            ->method('isUserLogged')
            ->willReturn(false);

        $callback = function ($request) {
        };

        $middleware = new AuthMiddleware($guard, $factory);

        $this->expectException(UnauthorizedHttpException::class);
        $middleware->handle($request, $callback);
    }
}
