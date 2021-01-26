<?php

namespace App\Service\Authentication;


use App\Entity\User;

interface AuthenticationInterface
{
    /**
     * Validates the user credentials
     *
     * @param $credentials
     * @return User
     */
    public function validate($credentials) : ?User;
}
