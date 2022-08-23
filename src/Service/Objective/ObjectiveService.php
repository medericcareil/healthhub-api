<?php

namespace App\Service\Objective;

use App\Entity\Objective;
use App\Entity\ObjectiveType;
use App\Service\AbstractApiService;
use App\Kernel\Exception\UserException;
use App\Kernel\Exception\InputException;
use App\Kernel\Exception\ObjectiveException;
use App\Kernel\Exception\ObjectiveTypeException;
use Symfony\Component\Uid\UuidV4;

/**
 * class ObjectiveService
 * @package App\Service\Objective
 */
class ObjectiveService extends AbstractApiService
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
        if (!isset($params['objective_type_id'])) {
            throw new InputException('La clé \'objective_type_id\' est requise');
        }
        if (!isset($params['value'])) {
            throw new InputException('La clé \'value\' est requise');
        }
    }

    /**
     * Check if ObjectiveType exist
     * @param string $objectiveTypeId 
     * @return ObjectiveType 
     * @throws ObjectiveTypeException 
     */
    private function isObjectiveType(string $objectiveTypeId): ObjectiveType
    {
        $objectiveType = $this->em->getRepository(ObjectiveType::class)->findOneBy(['id' => $objectiveTypeId]);
        if (!$objectiveType) {
            throw new ObjectiveTypeException('Le type d\'objectif n\'existe pas');
        }

        return $objectiveType;
    }

    /**
     * Check if last value is identical
     * @param array $params 
     * @return void 
     * @throws ObjectiveException 
     */
    private function isDifferentValue(array $params): void
    {
        $lastObjective = $this->em->getRepository(Objective::class)->findBy(['objective_type' => $params['objective_type_id']], ['created_at' => 'DESC'], 1);
        if (!empty($lastObjective)) {
            foreach ($lastObjective[0]->getValue() as $key => $value) {
                if ($value === $params['value']) {
                    throw new ObjectiveException('Valeurs identiques');   
                }
            }
        }
    }

    /**
     * Persists a Objective in the database
     * @param array $params 
     * @return Objective 
     * @throws UserException 
     * @throws ObjectiveTypeException 
     */
    public function persist(array $params): Objective
    {
        $user = $this->isUser($params['user_id']);
        $objectiveType = $this->isObjectiveType($params['objective_type_id']);
        $this->isDifferentValue($params);
        $objective = (new Objective())
            ->setUser($user)
            ->setObjectiveType($objectiveType)
            ->setValue([$params['value']]);

        $this->em->persist($objective);
        $this->em->flush();

        return $objective;
    }

    /**
     * Fetch all Objective from the User
     * @param string $userId 
     * @return Objective[] 
     */
    public function fetchAll(string $userId)
    {
        return $this->em->getRepository(Objective::class)->findBy(['user' => UuidV4::fromString($userId)], ['created_at' => 'DESC']);
    }
}
