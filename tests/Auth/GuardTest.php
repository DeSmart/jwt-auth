<?php

namespace test\JWTAuth\Auth;

use DeSmart\JWTAuth\Auth\Guard;
use DeSmart\JWTAuth\Exception\UserNotFoundException;
use Illuminate\Database\Eloquent\Model;
use Lcobucci\JWT\Token;

class GuardTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function it_logs_user_by_model()
    {
        $user = $this->createMock(Model::class);
        $guard = new Guard($this->createMock(Model::class));

        $guard->loginUser($user);

        $this->assertTrue($guard->isUserLogged());
        $this->assertSame($user, $guard->getUser());
    }

    /** @test */
    public function it_logs_user_by_token()
    {
        $uid = 1;
        $user = $this->createMock(Model::class);
        $model = $this->getMockBuilder(Model::class)
            ->disableOriginalConstructor()
            ->setMethods(['find'])
            ->getMock();

        $model->expects($this->once())
            ->method('find')
            ->with($this->equalTo($uid))
            ->willReturn($user);

        $token = $this->createMock(Token::class);
        $token->expects($this->any())
            ->method('getClaim')
            ->with($this->equalTo('uid'))
            ->willReturn($uid);

        $guard = new Guard($model);
        $guard->loginByToken($token);

        $this->assertTrue($guard->isUserLogged());
        $this->assertSame($user, $guard->getUser());
    }

    /** @test */
    public function it_fails_login_when_user_does_not_exist()
    {
        $uid = 1;
        $model = $this->getMockBuilder(Model::class)
            ->disableOriginalConstructor()
            ->setMethods(['find'])
            ->getMock();

        $model->expects($this->once())
            ->method('find')
            ->with($this->equalTo($uid))
            ->willReturn(null);

        $token = $this->createMock(Token::class);
        $token->expects($this->any())
            ->method('getClaim')
            ->with($this->equalTo('uid'))
            ->willReturn($uid);

        $guard = new Guard($model);

        $this->expectException(UserNotFoundException::class);
        $guard->loginByToken($token);
    }
}
