<?php

namespace App\Service;


use App\Entity\User;
use App\Entity\Note;

class UserHelper
{

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
        //@TODO:password should be encoded in DB.
        return $user->getPassword() === $password;
    }

}