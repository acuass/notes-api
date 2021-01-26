<?php

namespace App\Service\Authentication;

use App\Entity\User;
use App\Repository\UserRepository;


final class BasicHttpAuthentication implements AuthenticationInterface
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function validate($credentials) : ?User
    {
        $auth = base64_decode($credentials);
        list($email, $password) = explode(":", $auth);

        $user = $this->userRepository->findOneBy(['email' => $email]);

        return !empty($user) && $this->checkUserPassword($user,$password) ? $user : null;
    }

    /**
     * Returns true if the credentials match, false otherwise
     *
     * @param User $user
     * @param string $password
     * @return bool
     */
    private function checkUserPassword (User $user, string $password) : bool
    {
        return password_verify($password, $user->getPassword());
    }
}