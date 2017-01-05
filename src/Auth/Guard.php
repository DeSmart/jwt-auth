<?php

namespace DeSmart\JWTAuth\Auth;

use DeSmart\JWTAuth\Exception\UserNotFoundException;
use Illuminate\Database\Eloquent\Model;
use Lcobucci\JWT\Token;

class Guard
{

    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $userModel;

    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $user;

    public function __construct(Model $userModel)
    {
        $this->userModel = $userModel;
    }

    /**
     * @param Token $token
     */
    public function loginByToken(Token $token)
    {
        $id = $token->getClaim('uid');
        $user = $this->userModel->find($id);

        if (null === $user) {
            throw new  UserNotFoundException;
        }

        $this->user = $user;
    }

    /**
     * @param $user
     */
    public function loginUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return bool
     */
    public function isUserLogged(): bool
    {
        return null !== $this->user;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getUser()
    {
        return $this->user;
    }
}
