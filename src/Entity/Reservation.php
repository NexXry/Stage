<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ReservationRepository::class)
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="dtype", type="string")
 * @ORM\DiscriminatorMap({"reservation" = "Reservation",
"reservationTatoo" = "ReservationTatoo","reservationpiercing" = "ReservationPiercing"})
 *
 */
class Reservation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     *
     */
    private $DateReservation;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Etat;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $message;

    /**
     * @ORM\ManyToOne(targetEntity=Utilisateur::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $UnUtilisateur;

	/**
	 * Reservation constructor.
	 * @param $DateReservation
	 * @param $Etat
	 * @param $message
	 * @param $UnUtilisateur
	 */
	public function __construct($DateReservation, $Etat, $message, $UnUtilisateur)
	{
		$this->DateReservation = $DateReservation;
		$this->Etat = $Etat;
		$this->message = $message;
		$this->UnUtilisateur = $UnUtilisateur;
	}


	public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateReservation()
    {
        return $this->DateReservation;
    }

    public function setDateReservation($DateReservation): self
    {
        $this->DateReservation = $DateReservation;

        return $this;
    }

    public function getEtat()
    {
        return $this->Etat;
    }

    public function setEtat($Etat): self
    {
        $this->Etat = $Etat;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getUnUtilisateur()
    {
        return $this->UnUtilisateur;
    }

    public function setUnUtilisateur( $UnUtilisateur): self
    {
        $this->UnUtilisateur = $UnUtilisateur;

        return $this;
    }

	public function __toString()
	{
		return strval($this->id);
	}
}
