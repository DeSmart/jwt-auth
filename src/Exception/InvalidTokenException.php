<?php

namespace DeSmart\JWTAuth\Exception;

class InvalidTokenException extends \RuntimeException
{
    protected $code = 401;
}
