<?php

namespace App\DataFixtures;

use App\Entity\ObjectiveType;
use App\DataFixtures\UserFixtures;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ObjectiveTypeFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $objectiveType = new ObjectiveType();
        $objectiveType->setName('steps');
        $manager->persist($objectiveType);
        
        $manager->flush();
    }
    
    public function getDependencies() {
        return [
            UserFixtures::class,
        ];
    }
}
