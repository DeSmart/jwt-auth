<?php

namespace test\JWTAuth\Jwt;

use DeSmart\JWTAuth\Auth\Guard;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac;
use Lcobucci\JWT\Token;
use DeSmart\JWTAuth\Jwt\TokenFactory;

class TokenFactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function it_creates_token_for_user()
    {
        $expireTtl = '1 week';
        $secret = 'somuchsecrets';
        $now = new \DateTimeImmutable();
        $user = (object)['id' => 1];
        $expireAt = $now->add(\DateInterval::createFromDateString($expireTtl));

        $expectedToken = (new Builder)->setIssuedAt($now->getTimestamp())
            ->setExpiration($expireAt->getTimestamp())
            ->set('uid', 1)
            ->sign(new Hmac\Sha256, $secret)
            ->getToken();

        $factory = new TokenFactory($expireTtl, $secret, $now);

        $this->assertEquals($expectedToken, $factory->createForUser($user));
    }

    /** @test */
    public function it_creates_token_with_extra_claims()
    {
        $expireTtl = '1 week';
        $secret = 'somuchsecrets';
        $now = new \DateTimeImmutable();
        $user = (object)['id' => 1];
        $expireAt = $now->add(\DateInterval::createFromDateString($expireTtl));

        $expectedToken = (new Builder)->setIssuedAt($now->getTimestamp())
            ->setExpiration($expireAt->getTimestamp())
            ->set('foo', 'bar')
            ->set('uid', 1)
            ->sign(new Hmac\Sha256, $secret)
            ->getToken();

        $factory = new TokenFactory($expireTtl, $secret, $now);

        $this->assertEquals($expectedToken, $factory->createForUser($user, ['foo' => 'bar']));
    }
}
