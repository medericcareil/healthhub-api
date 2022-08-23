<?php

namespace App\DataFixtures;

use App\Entity\Objective;
use App\Repository\UserRepository;
use Doctrine\Persistence\ObjectManager;
use App\DataFixtures\ObjectiveTypeFixtures;
use App\Repository\ObjectiveTypeRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ObjectiveFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;

    /**
     * @var ObjectiveTypeRepository
     */
    private ObjectiveTypeRepository $objectiveTypeRepository;

    public function __construct(
        UserRepository $userRepository,
        ObjectiveTypeRepository $objectiveTypeRepository
    )
    {
        $this->userRepository = $userRepository;
        $this->objectiveTypeRepository = $objectiveTypeRepository;
    }

    public function load(ObjectManager $manager): void
    {
        $user = $this->userRepository->findOneBy(['email' => 'admin@exemple.fr']);
        $objectiveType = $this->objectiveTypeRepository->findOneBy(['name' => 'steps']);

        $objective = new Objective();
        $objective->setUser($user);
        $objective->setObjectiveType($objectiveType);
        $objective->setValue([10000]);
        $manager->persist($objective);

        $manager->flush();
    }

    public function getDependencies() {
        return [
            UserFixtures::class,
            ObjectiveTypeFixtures::class,
        ];
    }
}
 