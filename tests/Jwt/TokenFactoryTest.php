<?php

namespace test\JWTAuth\Jwt;

use DeSmart\JWTAuth\Exception\InvalidTokenException;
use DeSmart\JWTAuth\Jwt\TokenFactory;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac;
use Illuminate\Http\Request;

class TokenFactoryTest extends \PHPUnit_Framework_TestCase
{
    private $secret = 'somuchsecrets';

    private $expireTtl = '1 week';

    /** @test */
    public function it_creates_token_for_user()
    {
        $now = new \DateTimeImmutable();
        $user = (object)['id' => 1];
        $expireAt = $now->add(\DateInterval::createFromDateString($this->expireTtl));

        $expectedToken = (new Builder)->setIssuedAt($now->getTimestamp())
            ->setExpiration($expireAt->getTimestamp())
            ->set('uid', 1)
            ->sign(new Hmac\Sha256, $this->secret)
            ->getToken();

        $factory = $this->createFactory($now);

        $this->assertEquals($expectedToken, $factory->createForUser($user));
    }

    /** @test */
    public function it_creates_token_for_user_with_getter_method()
    {
        $now = new \DateTimeImmutable();

        $user = new User('foo');
        $expireAt = $now->add(\DateInterval::createFromDateString($this->expireTtl));

        $expectedToken = (new Builder)->setIssuedAt($now->getTimestamp())
            ->setExpiration($expireAt->getTimestamp())
            ->set('uid', 'foo')
            ->sign(new Hmac\Sha256, $this->secret)
            ->getToken();

        $factory = $this->createFactory($now);

        $this->assertEquals($expectedToken, $factory->createForUser($user));
    }

    /** @test */
    public function it_creates_token_with_extra_claims()
    {
        $now = new \DateTimeImmutable();
        $user = (object)['id' => 1];
        $expireAt = $now->add(\DateInterval::createFromDateString($this->expireTtl));

        $expectedToken = (new Builder)->setIssuedAt($now->getTimestamp())
            ->setExpiration($expireAt->getTimestamp())
            ->set('foo', 'bar')
            ->set('uid', 1)
            ->sign(new Hmac\Sha256, $this->secret)
            ->getToken();

        $factory = $this->createFactory($now);

        $this->assertEquals($expectedToken, $factory->createForUser($user, ['foo' => 'bar']));
    }

    /** @test */
    public function it_creates_token_from_request()
    {
        $user = (object)['id' => 1];
        $expectedToken = $this->createFactory()->createForUser($user);

        $request = $this->createMock(Request::class);
        $request->expects($this->any())
            ->method('header')
            ->with($this->equalTo('authorization'))
            ->willReturn("Bearer {$expectedToken}");

        $token = $this->createFactory()->createFromRequest($request);

        $this->assertEquals($expectedToken, $token);
    }

    /** @test */
    public function it_creates_null_token_on_invalid_request()
    {
        $factory = $this->createFactory();

        $request = $this->createMock(Request::class);
        $request->expects($this->any())
            ->method('header')
            ->with($this->equalTo('authorization'))
            ->willReturn(null);

        $this->assertNull($factory->createFromRequest($request));
    }

    /** @test */
    public function it_fails_on_invalid_token()
    {
        $factory = $this->createFactory();

        $request = $this->createMock(Request::class);
        $request->expects($this->any())
            ->method('header')
            ->with($this->equalTo('authorization'))
            ->willReturn('Bearer soo');

        $this->expectException(InvalidTokenException::class);
        $factory->createFromRequest($request);
    }

    /** @test */
    public function it_fails_when_token_is_not_verified()
    {
        $user = (object)['id' => 1];
        $invalidToken = (string) $this->createFactory(null, 'notsosecret')->createForUser($user);

        $request = $this->createMock(Request::class);
        $request->expects($this->any())
            ->method('header')
            ->with($this->equalTo('authorization'))
            ->willReturn("Bearer {$invalidToken}");

        $this->expectException(InvalidTokenException::class);
        $this->createFactory()->createFromRequest($request);
    }

    private function createFactory($now = null, string $secret = null): TokenFactory
    {
        return new TokenFactory($this->expireTtl, $secret ?? $this->secret, $now ?? new \DateTimeImmutable);
    }
}

class User {
    private $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }
}
