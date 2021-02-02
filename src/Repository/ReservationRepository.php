<?php

namespace App\Repository;

use App\Entity\Reservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Reservation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reservation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reservation[]    findAll()
 * @method Reservation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }


    public function findLast()
    {
	    $conn = $this->getEntityManager()->getConnection();
	    $sql = 'SELECT *
            FROM reservation r
            ORDER BY r.id DESC LIMIT 1';
	    $stmt = $conn->prepare($sql);
	    $stmt->execute([]);

	    return $stmt->fetchAllAssociative();
    }

	public function findIdUser($id)
	{
		$conn = $this->getEntityManager()->getConnection();
		$sql = 'SELECT id
            FROM reservation r
            where un_utilisateur_id = '.$id.'';
		$stmt = $conn->prepare($sql);
		$stmt->execute([]);

		return $stmt->fetchAllAssociative();
	}

	public function findIdlast()
	{
		$conn = $this->getEntityManager()->getConnection();
		$sql = 'SELECT id
            FROM reservation r
            ORDER BY r.id DESC LIMIT 1 ';
		$stmt = $conn->prepare($sql);
		$stmt->execute([]);

		return $stmt->fetchAllAssociative();
	}

	public function meinAll()
	{
		$conn = $this->getEntityManager()->getConnection();
		$sql = 'SELECT *
            FROM reservation r
            ORDER BY r.id DESC';
		$stmt = $conn->prepare($sql);
		$stmt->execute([]);

		return $stmt->fetchAllAssociative();
	}


    public function findOneByUserId($value)
    {
        return $this->createQueryBuilder('r')
	        ->andWhere('r.UnUtilisateur = :val')
            ->setParameter('val', $value)
	        ->orderBy('r.id',"ASC")
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
