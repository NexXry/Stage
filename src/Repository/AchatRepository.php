<?php

namespace App\Repository;

use App\Entity\Achat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Achat|null find($id, $lockMode = null, $lockVersion = null)
 * @method Achat|null findOneBy(array $criteria, array $orderBy = null)
 * @method Achat[]    findAll()
 * @method Achat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AchatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Achat::class);
    }

	public function findLast()
	{
		$conn = $this->getEntityManager()->getConnection();
		$sql = 'SELECT date_achat,description_achat,id_commande,recu,payment_amount,achat.id,quantite,image,utilisateur.nom,utilisateur.prenom FROM achat,produit,utilisateur WHERE achat.le_produit_id = produit.id AND achat.le_utilisateur_id = utilisateur.id and utilisateur.id = achat.le_utilisateur_id and produit.id = achat.le_produit_id ORDER By achat.id DESC LIMIT 3';
		$stmt = $conn->prepare($sql);
		$stmt->execute([]);

		return $stmt->fetchAllAssociative();
	}


    public function findByProduct($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.LeProduit = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }


    public function findIdCom($value): ?Achat
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.idCommande = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

}
