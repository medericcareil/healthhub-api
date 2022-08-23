<?php

namespace App\Service\Statistic;

use LogicException;
use RuntimeException;
use App\Entity\Activity;
use App\Entity\ActivityType;
use App\Kernel\Exception\ActivityTypeException;
use InvalidArgumentException;
use Doctrine\ORM\ORMException;
use Symfony\Component\Uid\UuidV4;
use App\Service\AbstractApiService;
use App\Repository\ActivityRepository;
use App\Kernel\Exception\InputException;
use App\Kernel\Exception\UserException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * class StatisticService
 * @package App\Service\Statistic
 */
class StatisticService extends AbstractApiService
{
    /**
     * @var ActivityRepository
     */
    private ActivityRepository $activityRepository;

    public function __construct(
        EntityManagerInterface $em, 
        UrlGeneratorInterface $router,
        ActivityRepository $activityRepository
    )
    {
        parent::__construct($em, $router);
        $this->activityRepository = $activityRepository;
    }

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
        if (!isset($params['start_date'])) {
            throw new InputException('La clé \'start_date\' est requise');
        }
        if (!isset($params['end_date'])) {
            throw new InputException('La clé \'end_date\' est requise');
        }
    }

    /**
     * Fetch all activities between two dates
     * @param array $params 
     * @return Activity[] 
     * @throws UserException 
     * @throws ActivityTypeException 
     * @throws InvalidArgumentException 
     * @throws RuntimeException 
     * @throws LogicException 
     * @throws ORMException 
     */
    public function fetchBetween(array $params)
    {   
        $user = $this->isUser($params['user_id']);
        $activityType = $this->em->getRepository(ActivityType::class)->findOneBy(['id' => UuidV4::fromString($params['activity_type_id'])]);
        if (!$activityType) {
            throw new ActivityTypeException('Le type d\'activité n\'existe pas');
        }
 
        return $this->activityRepository->findBetween($user, $activityType, $params['start_date'], $params['end_date']);
    }
}
