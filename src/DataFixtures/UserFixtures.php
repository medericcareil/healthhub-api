<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    /**
     * @var UserPasswordHasherInterface
     */
    private $encoder;

    public function __construct(UserPasswordHasherInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        
        $array = [
            'email'        => 'admin@exemple.fr',
            'pseudo'       => 'Michel76',
            'password'     => $this->encoder->hashPassword($user, 'Mdpadmin'),
            'roles'        => ['ROLE_USER', 'ROLE_ADMIN'],
            'gender'       => true,
            'birthdate'    => new \DateTimeImmutable('1990-10-10'),
            'is_validated' => true
        ];

        $manager->persist($user::fromArray($array));

        $user2 = new User();
        
        $array2 = [
            'email'        => 'user@exemple.fr',
            'pseudo'       => 'Bobby',
            'password'     => $this->encoder->hashPassword($user2, 'Mdpuser'),
            'gender'       => true,
            'birthdate'    => new \DateTimeImmutable('1984-06-22'),
            'is_validated' => true
        ];

        $manager->persist($user2::fromArray($array2));
        
        $manager->flush();
    }
}
