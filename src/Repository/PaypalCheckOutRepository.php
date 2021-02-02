<?php

namespace App\Repository;

use App\Entity\PaypalCheckOut;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PaypalCheckOut|null find($id, $lockMode = null, $lockVersion = null)
 * @method PaypalCheckOut|null findOneBy(array $criteria, array $orderBy = null)
 * @method PaypalCheckOut[]    findAll()
 * @method PaypalCheckOut[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaypalCheckOutRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PaypalCheckOut::class);
    }

    // /**
    //  * @return PaypalCheckOut[] Returns an array of PaypalCheckOut objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PaypalCheckOut
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
