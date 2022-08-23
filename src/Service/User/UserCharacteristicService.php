<?php

namespace App\Service\User;

use App\Entity\UserCharacteristic;
use App\Kernel\Exception\InputException;
use App\Kernel\Exception\UserException;
use App\Service\AbstractApiService;

/**
 * class UserCharacteristicService
 * @package App\Service\User
 */
class UserCharacteristicService extends AbstractApiService
{
    /**
     * Validate inputs
     * @param null|array $params 
     * @return void 
     * @throws InputException 
     */
    public function validateInputs(?array $params): void
    {
        $this->validateInput($params);
        if (!isset($params['weight']) || !is_numeric($params['weight'])) {
            throw new InputException('La clé \'weight\' est requise et doit être une valeur numérique');
        }
        if (!isset($params['height']) || !is_numeric($params['height'])) {
            throw new InputException('La clé \'height\' est requise et doit être une valeur numérique');
        }
    }

    /**
     * Format inputs
     * @param array $params 
     * @return void 
     * @throws UserException 
     */
    public function formatInputs(array &$params): void
    {
        $params['user_id'] = $this->isUser($params['user_id']);
    }

    /**
     * Check if last characteristics is identical
     * @param array $params 
     * @return void 
     * @throws UserException 
     */
    private function isDifferentCharacteristics(array $params): void
    {
        $lastCharacteristic = $this->em->getRepository(UserCharacteristic::class)->findBy(['user' => $params['user_id']], ['created_at' => 'DESC'], 1);
        if (!empty($lastCharacteristic)) {
            if ($lastCharacteristic[0]->getWeight() == $params['weight'] && $lastCharacteristic[0]->getHeight() == $params['height']) {
                throw new UserException('Caractéristiques identiques'); 
            }
        }
    }

    /**
     * Persists a UserCharacteristic in the database
     * @param array $params 
     * @return UserCharacteristic 
     */
    public function persist(array $params): UserCharacteristic
    {
        $this->isDifferentCharacteristics($params);
        $userCharacteristic = UserCharacteristic::fromArray($params);

        $this->em->persist($userCharacteristic);
        $this->em->flush();

        return $userCharacteristic;
    }

    /**
     * Fetch all UserCharacteristic from user_id
     * @param array $params 
     * @return UserCharacteristic[] 
     * @throws UserException 
     */
    public function fetchAll(array $params)
    {
        return $this->em->getRepository(UserCharacteristic::class)->findBy(['user' => $params['user_id']], ['created_at' => 'DESC']);
    }
}
