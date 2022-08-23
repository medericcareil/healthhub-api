<?php

namespace App\DataFixtures;

use DateTime;
use DateInterval;
use DateTimeImmutable;
use App\Entity\Activity;
use App\DataFixtures\UserFixtures;
use App\Repository\UserRepository;
use Doctrine\Persistence\ObjectManager;
use App\Service\Activity\ActivityService;
use App\DataFixtures\ActivityTypeFixtures;
use App\Repository\ActivityTypeRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use App\DataFixtures\UserCharacteristicFixtures;
use App\Repository\UserCharacteristicRepository;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ActivityFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;

    /**
     * @var ActivityTypeRepository
     */
    private ActivityTypeRepository $activityTypeRepository;
    
    /**
     * @var UserCharacteristicRepository
     */
    private UserCharacteristicRepository $userCharacteristicRepository;

    /**
     * @var ActivityService
     */
    private ActivityService $activityService;

    public function __construct(
        UserRepository $userRepository,
        ActivityTypeRepository $activityTypeRepository,
        UserCharacteristicRepository $userCharacteristicRepository,
        ActivityService $activityService
    )
    {
        $this->userRepository = $userRepository;
        $this->activityTypeRepository = $activityTypeRepository;
        $this->userCharacteristicRepository = $userCharacteristicRepository;
        $this->activityService = $activityService;
    }

    public function load(ObjectManager $manager): void
    {
        $user = $this->userRepository->findOneBy(['email' => 'admin@exemple.fr']);
        $userCharacteristic = $this->userCharacteristicRepository->findOneBy(['user' => $user], ['created_at' => 'DESC']);
        $activityType = $this->activityTypeRepository->findOneBy(['name' => 'steps']);
        $activityType2 = $this->activityTypeRepository->findOneBy(['name' => 'calories']);

        $start = '2022-01-01';

        $dateDiff = (array)date_diff(new DateTime($start), new DateTime('now'));
        
        for ($k = 0; $k < $dateDiff['days']; $k++) { 
            $tempStartDate = new DateTime($start);
            $setStartTime = $tempStartDate->setTime(0, 0);
            $startDate = $setStartTime->add(new DateInterval('P' . $k . 'D'));

            $tempEndDate = new DateTime($start);
            $setEndTime = $tempEndDate->setTime(23, 59, 59, 999999);
            $endDate = $setEndTime->add(new DateInterval('P' . $k . 'D'));

            $steps = rand(0, 18000);
            $calories = $this->activityService->stepsToCalories($user->getGender(), $userCharacteristic->getWeight(), $steps);
            
            $value = [
                'value' => $steps,
                'start_date' => $startDate,
                'end_date' => $endDate
            ];

            $activity = new Activity();
            $activity->setUser($user);
            $activity->setActivityType($activityType);
            $activity->setValue($value);
            $activity->setCreatedAt(new DateTimeImmutable($startDate->format('Y-m-d')));
            $manager->persist($activity); 

            $value2 = [
                'value' => $calories,
                'start_date' => $startDate,
                'end_date' => $endDate
            ];
       
            $activity2 = new Activity();
            $activity2->setUser($user);
            $activity2->setActivityType($activityType2);
            $activity2->setValue($value2);
            $activity2->setCreatedAt(new DateTimeImmutable($startDate->format('Y-m-d')));
            $manager->persist($activity2); 
        }
        
        $manager->flush();
    }

    public function getDependencies() {
        return [
            UserFixtures::class,
            ActivityTypeFixtures::class,
            UserCharacteristicFixtures::class
        ];
    }
}
