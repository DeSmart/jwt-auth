<?php

namespace test\JWTAuth\Middleware;

use DeSmart\JWTAuth\Auth\Guard;
use DeSmart\JWTAuth\Jwt\TokenFactory;
use DeSmart\JWTAuth\Middleware\TokenRefreshMiddleware;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Lcobucci\JWT\Token;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Response;

class TokenRefreshMiddlewareTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function it_returns_response_when_user_is_not_logged_in()
    {
        $guard = $this->createMock(Guard::class);
        $factory = $this->createMock(TokenFactory::class);

        $guard->expects($this->any())
            ->method('isUserLogged')
            ->willReturn(false);

        $response = $this->createMock(Response::class);
        $callback = function () use ($response) {
            return $response;
        };

        $middleware = new TokenRefreshMiddleware($guard, $factory);
        $result = $middleware->handle($this->createMock(Request::class), $callback);

        $this->assertSame($response, $result);
    }

    /** @test */
    public function it_regenerates_token_for_logged_user()
    {
        $guard = $this->createMock(Guard::class);
        $factory = $this->createMock(TokenFactory::class);
        $user = $this->createMock(Model::class);
        $token = $this->createMock(Token::class);
        $headersBag = $this->createMock(HeaderBag::class);

        $guard->expects($this->any())
            ->method('isUserLogged')
            ->willReturn(true);

        $guard->expects($this->any())
            ->method('getUser')
            ->willReturn($user);

        $token->expects($this->any())
            ->method('__toString')
            ->willReturn('teh.token');

        $factory->expects($this->once())
            ->method('createForUser')
            ->with($this->equalTo($user))
            ->willReturn($token);

        $headersBag->expects($this->once())
            ->method('set')
            ->with(
                $this->equalTo('Authorization'),
                $this->equalTo('Bearer teh.token')
            );

        $response = new Response();
        $response->headers = $headersBag;

        $callback = function () use ($response) {
            return $response;
        };

        $middleware = new TokenRefreshMiddleware($guard, $factory);
        $middleware->handle($this->createMock(Request::class), $callback);
    }
}
