<?php

namespace DeSmart\JWTAuth\Auth;

use Lcobucci\JWT\Token;
use DeSmart\JWTAuth\Exception\UserNotFoundException;

class Guard
{

    /**
     * @var string
     */
    protected $userModelClass;

    public function __construct(string $userModelClass)
    {
        $this->userModelClass = $userModelClass;
    }

    /**
     * @var User
     */
    protected $user;

    public function foo($id)
    {
        $user = ($this->userModelClass)::find($id);
        dd($user);
    }

    public function loginByToken(Token $token)
    {
        $id = $token->getClaim('uid');

        $user = ($this->userModelClass)::find($id);
dd($user);





        $uid = $token->getClaim('uid');

        try {
            $this->user = $this->usersRepository->get($uid);
        } catch (UserNotFoundException $e) {
            // do nothing here
        }
    }

    public function loginUser(User $user)
    {
        $this->user = $user;
    }

    public function isUserLogged(): bool
    {
        return null !== $this->user;
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
