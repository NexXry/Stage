<?php

namespace App\Repository;

use App\Entity\ForgotPass;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ForgotPass|null find($id, $lockMode = null, $lockVersion = null)
 * @method ForgotPass|null findOneBy(array $criteria, array $orderBy = null)
 * @method ForgotPass[]    findAll()
 * @method ForgotPass[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ForgotPassRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ForgotPass::class);
    }

    // /**
    //  * @return ForgotPass[] Returns an array of ForgotPass objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    public function findOneByCode($value): ?ForgotPass
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.code = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
