<?php

namespace App\Repository;

use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Utilisateur|null find($id, $lockMode = null, $lockVersion = null)
 * @method Utilisateur|null findOneBy(array $criteria, array $orderBy = null)
 * @method Utilisateur[]    findAll()
 * @method Utilisateur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UtilisateurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Utilisateur::class);
    }

	public function findIdUser($id)
	{
		$conn = $this->getEntityManager()->getConnection();
		$sql = 'SELECT *
            FROM utilisateur r
            where r.id = '.$id.'';
		$stmt = $conn->prepare($sql);
		$stmt->execute([]);

		return $stmt->fetchAllAssociative();
	}


    public function findByMail($mail): ?Utilisateur
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.mail = :val')
            ->setParameter('val', $mail)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
