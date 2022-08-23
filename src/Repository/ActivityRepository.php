<?php

namespace App\Repository;

use LogicException;
use App\Entity\User;
use RuntimeException;
use DateTimeImmutable;
use App\Entity\Activity;
use App\Entity\ActivityType;
use InvalidArgumentException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query\Parameter;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Activity|null find($id, $lockMode = null, $lockVersion = null)
 * @method Activity|null findOneBy(array $criteria, array $orderBy = null)
 * @method Activity[]    findAll()
 * @method Activity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActivityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Activity::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Activity $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Activity $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }


    /**
     * Return all activities between two dates
     * 
     * @param User $user 
     * @param ActivityType $activityType 
     * @param string $date1 
     * @param string $date2 
     * @return Activity[] 
     * @throws InvalidArgumentException 
     * @throws RuntimeException 
     * @throws LogicException 
     * @throws ORMException 
     */
    public function findBetween(User $user, ActivityType $activityType, string $date1, string $date2)
    {
        return $this->createQueryBuilder('a')
            ->leftJoin('a.user', 'u')
            ->leftJoin('a.activity_type', 'a_type')
            ->where('u.email = :email')
            ->andWhere('a_type.name = :name')
            ->andWhere('a.created_at BETWEEN :date1 AND :date2')
            ->setParameters(new ArrayCollection([
                new Parameter('email', $user->getEmail()),
                new Parameter('name', $activityType->getName()),
                new Parameter('date1', new DateTimeImmutable($date1)),
                new Parameter('date2', new DateTimeImmutable($date2)),
            ]))
            ->orderBy('a.created_at', 'DESC')
            ->getQuery()
            ->getResult()
            ;
    }

    // /**
    //  * @return Activity[] Returns an array of Activity objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Activity
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
