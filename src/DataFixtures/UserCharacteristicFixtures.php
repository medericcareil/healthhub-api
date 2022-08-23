<?php

namespace App\DataFixtures;

use App\DataFixtures\UserFixtures;
use App\Entity\UserCharacteristic;
use App\Repository\UserRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class UserCharacteristicFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function load(ObjectManager $manager): void
    {
        $user = $this->userRepository->findOneBy(['email' => 'admin@exemple.fr']);
        $userCharacteristic = new UserCharacteristic();
        $userCharacteristic->setUser($user);
        $userCharacteristic->setWeight(85);
        $userCharacteristic->setHeight(185);
        $manager->persist($userCharacteristic);
        
        $manager->flush();
    }

    public function getDependencies() { 
        return [
            UserFixtures::class,
        ];
    }
}
