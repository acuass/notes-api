<?php

namespace App\Service\Authentication;


use App\Repository\UserRepository;

final class AuthenticationFactory
{

    private $userRepository;


    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Creates an importer object.
     *
     * @param string $authorizationType
     *
     * @return AuthenticationInterface | null
     */
    public function create($authorizationType = "Basic")
    {
        switch ($authorizationType) {
            case "Basic":
                //@TODO:find how to inject it via services.yml
                return new BasicHttpAuthentication($this->userRepository);

            case "Bearer":
                return new BearerAuthentication($this->userRepository);
        }

        return null;
    }
}
