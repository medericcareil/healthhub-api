<?php

namespace App\Service\User;

use App\Entity\User;
use App\Service\AbstractApiService;
use App\Kernel\Exception\InputException;
use App\Kernel\Exception\UserException;

/**
 * class UserService
 * @package App\Service\User
 */
class UserService extends AbstractApiService {
    /**
     * Validate inputs
     * @param null|array $params 
     * @return void 
     * @throws InputException 
     */
    public function validateInputs(?array $params): void
    {
        $this->validateInput($params);
        if (!isset($params['pseudo'])) {
            throw new InputException('La clé \'pseudo\' est requise');
        }
    }

    /**
     * Update User
     * @param array $params 
     * @return User 
     * @throws UserException 
     */
    public function update(array $params): User
    {
        $user = $this->isUser($params['user_id']);
        $user->setPseudo($params['pseudo']);
        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }
}
