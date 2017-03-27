<?php

namespace DeSmart\JWTAuth\Jwt;

use Illuminate\Http\Request;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac;
use Lcobucci\JWT\Token;
use DeSmart\JWTAuth\Exception\InvalidTokenException;

class TokenFactory
{

    /**
     * Token expiration ttl
     *
     * @example '1 hour'
     * @var string
     */
    protected $tokenExpTtl;

    /**
     * @var string
     */
    protected $tokenSecret;

    /**
     * @var \DateTimeImmutable
     */
    protected $now;

    public function __construct(string $tokenExpTtl, string $tokenSecret, \DateTimeImmutable $now = null)
    {
        $this->tokenExpTtl = $tokenExpTtl;
        $this->tokenSecret = $tokenSecret;
        $this->now = $now ?? new \DateTimeImmutable;
    }

    /**
     * Generates token based on user entity and current request
     *
     * @param User $user
     * @param array $extraClaims
     * @return Token
     */
    public function createForUser($user, array $extraClaims = []): Token
    {
        $expiration = $this->now->add(\DateInterval::createFromDateString($this->tokenExpTtl));

        $token = (new Builder)->setIssuedAt($this->now->getTimestamp())
            ->setExpiration($expiration->getTimestamp());

        $claims = ['uid' => is_callable([$user, 'getId']) ? $user->getId() : $user->id];

        foreach (array_merge($extraClaims, $claims) as $key => $value) {
            $token->set($key, $value);
        }

        return $token->sign($this->getSigner(), $this->tokenSecret)
            ->getToken();
    }

    /**
     * Creates token based on 'Authorization' header
     *
     * @param Request $request
     * @return Token|null
     * @throws \UnexpectedValueException when header format is invalid
     * @throws InvalidTokenException when token didn't pass HMAC verification
     */
    public function createFromRequest(Request $request)
    {
        $authorization = $request->header('authorization');

        if (null === $authorization) {
            return null;
        }

        $token = preg_replace('/^Bearer\s+/', '', $authorization);

        try {
            $token = (new Parser)->parse($token);
        } catch (\InvalidArgumentException $exception) {
            throw new InvalidTokenException($exception->getMessage());
        }

        $signer = $this->getSigner();

        if (false === $token->verify($signer, $this->tokenSecret)) {
            throw new InvalidTokenException;
        }

        return $token;
    }

    /**
     * Return token signer
     *
     * @return Hmac
     */
    protected function getSigner(): Hmac
    {
        return new Hmac\Sha256;
    }
}
