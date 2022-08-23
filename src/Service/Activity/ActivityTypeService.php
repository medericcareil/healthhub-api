<?php

namespace App\Service\Activity;

use App\Entity\ActivityType;
use App\Service\AbstractApiService;
use App\Kernel\Exception\InputException;
use App\Kernel\Exception\ActivityTypeException;

/**
 * class ActivityTypeService
 * @package App\Service\Activity
 */
class ActivityTypeService extends AbstractApiService
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
     * Check if ActivityType exist in database
     * @param string $nameType 
     * @return void 
     * @throws ActivityTypeException 
     */
    private function isActivityTypeExist(string $nameType): void
    {
        $objectivesTypes = $this->em->getRepository(ActivityType::class)->findOneBy(['name' => $nameType]);
        if ($objectivesTypes) {
            throw new ActivityTypeException(sprintf('Le type d\'activité \'%s\' existe déjà', $nameType));
        }
    }

    /**
     * Persists a ActivityType in the database
     * @param array $params 
     * @return ActivityType 
     * @throws ActivityTypeException 
     */
    public function persist(array $params): ActivityType
    {
        $this->isActivityTypeExist($params['name_type']);

        $activityType = ActivityType::fromArray($params);

        $this->em->persist($activityType);
        $this->em->flush();

        return $activityType;
    }

    /**
     * Fetch all ActivityType
     * @return ActivityType[]
     */
    public function fetchAll()
    {
        return $this->em->getRepository(ActivityType::class)->findBy([], ['name' => 'ASC']);
    }
}
