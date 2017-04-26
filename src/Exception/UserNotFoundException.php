<?php

namespace DeSmart\JWTAuth\Exception;

class UserNotFoundException extends \RuntimeException
{
    protected $code = 401;
}
