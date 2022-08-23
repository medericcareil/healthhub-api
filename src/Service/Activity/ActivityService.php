<?php

namespace App\Service\Activity;

use App\Entity\Activity;
use App\Entity\ActivityType;
use Symfony\Component\Uid\UuidV4;
use App\Service\AbstractApiService;
use App\Kernel\Exception\InputException;
use App\Kernel\Exception\ActivityTypeException;

/**
 * class ActivityService
 * @package App\Service\Activity
 */
class ActivityService extends AbstractApiService
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
        if (!isset($params['activity_type_id'])) {
            throw new InputException('La clé \'activity_type_id\' est requise');
        }
        if (!isset($params['data'])) {
            throw new InputException('La clé \'data\' est requise');
        }
    }

    /**
     * Check if ActivityType exist
     * @param string $activityTypeId 
     * @return ActivityType 
     * @throws ActivityTypeException 
     */
    private function isActivityType(string $activityTypeId): ActivityType
    {
        $activityType = $this->em->getRepository(ActivityType::class)->findOneBy(['id' => $activityTypeId]);
        if (!$activityType) {
            throw new ActivityTypeException('Le type d\'activité n\'existe pas');
        }

        return $activityType;
    }

    /**
     * Persists a Activity in the database
     * @param array $params 
     * @return Activity 
     * @throws UserException 
     * @throws ActivityTypeException 
     */
    public function persist(array $params): Activity
    {
        $user = $this->isUser($params['user_id']);
        $activityType = $this->isActivityType($params['activity_type_id']);
        $activity = (new Activity())
            ->setUser($user)
            ->setActivityType($activityType)
            ->setValue([$params['data']]);

        $this->em->persist($activity);
        $this->em->flush();

        return $activity;
    }


    /**
     * Fetch all Activity from the User
     * @param string $userId 
     * @return Activity[] 
     */
    public function fetchAll(string $userId)
    {
        return $this->em->getRepository(Activity::class)->findBy(['user' => UuidV4::fromString($userId)], ['created_at' => 'DESC']);
    }

    /**
     * Convert the number of steps taken into calories burned
     * @param bool $gender 
     * @param int $weight 
     * @param int $stepsNbr 
     * @return int 
     */
    public function stepsToCalories(bool $gender, int $weight, int $stepsNbr): int
    {
        $menCourber = [
            40 => 0.022, 
            50 => 0.028, 
            60 => 0.034, 
            70 => 0.040, 
            80 => 0.046, 
            90 => 0.052, 
            100 => 0.058, 
            110 => 0.0644, 
            120 => 0.0706, 
            130 => 0.0771, 
            140 => 0.0817
        ];

        $womenCourber = [
            40 => 0.022, 
            50 => 0.027, 
            60 => 0.032, 
            70 => 0.037, 
            80 => 0.042, 
            90 => 0.0472, 
            100 => 0.0526, 
            110 => 0.0582, 
            120 => 0.0642, 
            130 => 0.0705, 
            140 => 0.0771
        ];

        if (!$gender) {
            $arr = $menCourber;
        } else {
            $arr = $womenCourber;
        }
        $w = intval(round($weight, -1));
        if ($w < 40) {
            $w = 40;
        }
        if ($w > 140) {
            $w = 140;
        }
        foreach ($arr as $key => $value) {
            if ($key === $w) {
                return intval($value * $stepsNbr);
            }
        }
    }
}
