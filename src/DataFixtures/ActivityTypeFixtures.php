<?php

namespace App\DataFixtures;

use App\Entity\ActivityType;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ActivityTypeFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $activityType = new ActivityType();
        $activityType->setName('steps');
        $manager->persist($activityType);

        $activityType2 = new ActivityType();
        $activityType2->setName('calories');
        $manager->persist($activityType2);

        $manager->flush();
    }

    public function getDependencies() {
        return [
            UserFixtures::class,
        ];
    }
}
