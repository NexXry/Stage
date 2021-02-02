<?php

namespace App\Entity;

use App\Repository\AchatRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AchatRepository::class)
 */
class Achat
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $DateAchat;

    /**
     * @ORM\Column(type="float")
     */
    private $payment_amount;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $DescriptionAchat;

	/**
	 * @ORM\Column(type="string", length=255)
	 */
	private $idCommande;

    /**
     * @ORM\Column(type="string")
     */
    private $Recu;

    /**
     * @ORM\ManyToOne(targetEntity=Utilisateur::class, inversedBy="achats")
     */
    private $LeUtilisateur;

    /**
     * @ORM\ManyToOne(targetEntity=Produit::class, inversedBy="achats")
     */
    private $LeProduit;


    public function getId(): ?int
    {
        return $this->id;
    }



	public function getPaymentAmount()
	{
		return $this->payment_amount;
	}


	public function setPaymentAmount($payment_amount)
	{
		$this->payment_amount = $payment_amount;
		return $this;
	}




	/**
	 * @return mixed
	 */
	public function getIdCommande()
	{
		return $this->idCommande;
	}

	/**
	 * @param mixed $idCommande
	 * @return Achat
	 */
	public function setIdCommande($idCommande)
	{
		$this->idCommande = $idCommande;
		return $this;
	}




    public function getDateAchat()
    {
        return $this->DateAchat;
    }

    public function setDateAchat($DateAchat): self
    {
        $this->DateAchat = $DateAchat;

        return $this;
    }
    

    public function getDescriptionAchat()
    {
        return $this->DescriptionAchat;
    }

    public function setDescriptionAchat($DescriptionAchat): self
    {
        $this->DescriptionAchat = $DescriptionAchat;

        return $this;
    }

    public function getRecu()
    {
        return $this->Recu;
    }

    public function setRecu($Recu): self
    {
        $this->Recu = $Recu;

        return $this;
    }

    public function getLeUtilisateur(): ?Utilisateur
    {
        return $this->LeUtilisateur;
    }

    public function setLeUtilisateur(?Utilisateur $LeUtilisateur): self
    {
        $this->LeUtilisateur = $LeUtilisateur;

        return $this;
    }

    public function getLeProduit(): ?Produit
    {
        return $this->LeProduit;
    }

    public function setLeProduit(?Produit $LeProduit): self
    {
        $this->LeProduit = $LeProduit;

        return $this;
    }

	public function __toString()
	{
		return strval($this->payment_amount);
	}


}
