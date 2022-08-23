<?php

namespace App\Service\Objective;

use App\Entity\ObjectiveType;
use App\Service\AbstractApiService;
use App\Kernel\Exception\InputException;
use App\Kernel\Exception\ObjectiveTypeException;

class ObjectiveTypeService extends AbstractApiService
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
        if (!isset($params['name_type'])) {
            throw new InputException('La clé \'name_type\' est requise');
        }
    }

    /**
     * Check if ObjectiveType exist in database
     * @param string $nameType 
     * @return void 
     * @throws ObjectiveTypeException 
     */
    private function isObjectiveTypeExist(string $nameType): void
    {
        $objectivesTypes = $this->em->getRepository(ObjectiveType::class)->findOneBy(['name' => $nameType]);
        if ($objectivesTypes) {
            throw new ObjectiveTypeException(sprintf('Le type d\'objectif \'%s\' existe déjà', $nameType));
        }
    }

    /**
     * Persists a ObjectiveType in the database
     * @param array $params 
     * @return ObjectiveType 
     * @throws ObjectiveTypeException 
     */
    public function persist(array $params): ObjectiveType
    {
        $this->isObjectiveTypeExist($params['name_type']);

        $objectiveType = ObjectiveType::fromArray($params);

        $this->em->persist($objectiveType);
        $this->em->flush();

        return $objectiveType;
    }

    /**
     * Fetch all ObjectiveType
     * @return ObjectiveType[]
     */
    public function fetchAll()
    {
        return $this->em->getRepository(ObjectiveType::class)->findBy([], ['name' => 'ASC']);
    }
}
