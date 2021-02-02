<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CategoryRepository::class)
 */
class Category
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity=Produit::class, mappedBy="LaCategorie")
     * @ORM\Column(type="string")
     */
    private $libelle;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle()
    {
        return $this->libelle;
    }

	/**
	 * @param mixed $libelle
	 * @return Category
	 */
	public function setLibelle($libelle)
	{
		$this->libelle = $libelle;
		return $this;
	}



	public function __toString()
	{
		return $this->libelle;
	}


}
