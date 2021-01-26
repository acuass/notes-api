<?php

namespace App\Service\Authentication;


use App\Entity\User;
use App\Repository\UserRepository;

final class BearerAuthentication implements AuthenticationInterface
{

    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function validate($credentials) : User
    {
        //@TODO: to implement
        return null;
    }
}