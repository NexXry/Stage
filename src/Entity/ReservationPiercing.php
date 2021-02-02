<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="ReservationPiercing")
 * @ORM\Entity(repositoryClass=ReservationRepository::class)
 * @ORM\InheritanceType("SINGLE_TABLE")
 */
class ReservationPiercing extends Reservation
{

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $partieCorps;


	/**
	 * @ORM\Column(type="string", length=255)
	 */
	private $allergie;

	/**
	 * @return mixed
	 */
	public function getPartieCorps()
	{
		return $this->partieCorps;
	}

	/**
	 * @param mixed $partieCorps
	 * @return ReservationTatoo
	 */
	public function setPartieCorps($partieCorps)
	{
		$this->partieCorps = $partieCorps;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getAllergie()
	{
		return $this->allergie;
	}

	/**
	 * @param mixed $allergie
	 * @return ReservationTatoo
	 */
	public function setAllergie($allergie)
	{
		$this->allergie = $allergie;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getTaille()
	{
		return "";
	}


}
