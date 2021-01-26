<?php

namespace App\Service;


use App\Entity\User;
use App\Entity\Note;
use App\Repository\UserRepository;
use App\Service\Authentication\AuthenticationFactory;

class UserHelper
{

    private $userRepository;
    private $authenticationFactory;

    public function __construct(UserRepository $userRepository, AuthenticationFactory $authenticationFactory)
    {
        $this->userRepository = $userRepository;
        $this->authenticationFactory = $authenticationFactory;
    }

    /**
     * Returns true if the user is the owner of the note, false otherwise
     *
     * @param User $user
     * @param Note $note
     * @return bool
     */
    public function isUserOwnerOfNote(User $user, Note $note): bool
    {
        return $note->getUser()->getEmail() === $user->getEmail();
    }

    /**
     * Returns true if the credentials match, false otherwise
     *
     * @param User $user
     * @param string $password
     * @return bool
     */
    public function checkUserPassword(User $user, string $password) : bool
    {
        return password_verify($password, $user->getPassword());
    }


    public function checkUserAuthorization(string $auth)
    {
        if (empty($auth)) return null;

        list($authType, $credentials) = explode(" ", $auth);

        $authenticator = $this->authenticationFactory->create($authType);
        return $authenticator->validate($credentials);

    }

}