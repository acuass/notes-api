<?php

namespace App\Service;


use App\Entity\User;
use App\Entity\Note;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;

class UserHelper
{

    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
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

    /**
     * Returns the user if the access is granted. Null otherwise
     * @param Request $request
     *
     * @return \App\Entity\User|null
     */
    public function isUserAuthorizedByBasicHttp(Request $request)
    {
        //@TODO: different types of authorization? Explode, and with a factory get the correct manager based on the type (basic, bearer, etc...)
        $auth = $request->headers->get("Authorization");
        if (empty($auth)) return null;

        list($authType, $credentials) = explode(" ", $auth);
        $credentials = base64_decode($credentials);
        list($email, $password) = explode(":", $credentials);

        $user = $this->userRepository->findOneBy(['email' => $email]);

        return !empty($user) && $this->checkUserPassword($user,$password) ? $user : null;
    }
}