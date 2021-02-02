<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\UtilisateurRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=UtilisateurRepository::class)
 * @UniqueEntity(fields={"mail"}, message="Mail déja dans la base de donnée!")
 */
class Utilisateur implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     *
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $mail;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     *
     */
    private $telephone;

    /**
     * @ORM\Column(type="string", length=255)
     *
     */
    private $RoleUser;

	/**
	 * @ORM\Column(type="boolean")
	 *
	 */
	private $majeur;
    

    /**
     * @ORM\OneToMany(targetEntity=Article::class, mappedBy="UnUtilisateur")
     */
    private $LesArticles;

    /**
     * @ORM\OneToMany(targetEntity=Achat::class, mappedBy="LeUtilisateur")
     */
    private $achats;


    public function getId(): ?int
    {
        return $this->id;
    }

	/**
	 * @return mixed
	 */
	public function getMajeur()
	{
		return $this->majeur;
	}

	/**
	 * @param mixed $majeur
	 * @return Utilisateur
	 */
	public function setMajeur($majeur)
	{
		$this->majeur = $majeur;
		return $this;
	}



    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom()
    {
        return $this->prenom;
    }

    public function setPrenom($prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): self
    {
        $this->mail = $mail;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getRoleUser()
    {
        return [$this->RoleUser];
    }

    public function setRoleUser($RoleUser)
    {
        $this->RoleUser = $RoleUser;
    }

    /**
     * @return Collection|Produit[]
     */
    public function getLesAchats(): Collection
    {
        return $this->LesAchats;
    }

    public function addLesAchat(Produit $lesAchat): self
    {
        if (!$this->LesAchats->contains($lesAchat)) {
            $this->LesAchats[] = $lesAchat;
        }

        return $this;
    }

    public function removeLesAchat(Produit $lesAchat): self
    {
        $this->LesAchats->removeElement($lesAchat);

        return $this;
    }

    /**
     * @return Collection|Article[]
     */
    public function getLesArticles(): Collection
    {
        return $this->LesArticles;
    }

    public function addLesArticle(Article $lesArticle): self
    {
        if (!$this->LesArticles->contains($lesArticle)) {
            $this->LesArticles[] = $lesArticle;
            $lesArticle->setUnUtilisateur($this);
        }

        return $this;
    }

    public function removeLesArticle(Article $lesArticle): self
    {
        if ($this->LesArticles->removeElement($lesArticle)) {
            // set the owning side to null (unless already changed)
            if ($lesArticle->getUnUtilisateur() === $this) {
                $lesArticle->setUnUtilisateur(null);
            }
        }

        return $this;
    }

    public function getAchat(): ?Achat
    {
        return $this->achat;
    }

    public function setAchat(?Achat $achat): self
    {
        $this->achat = $achat;

        return $this;
    }

    /**
     * @return Collection|Achat[]
     */
    public function getAchats(): Collection
    {
        return $this->achats;
    }

    public function addAchat(Achat $achat): self
    {
        if (!$this->achats->contains($achat)) {
            $this->achats[] = $achat;
            $achat->setLeUtilisateur($this);
        }

        return $this;
    }

    public function removeAchat(Achat $achat): self
    {
        if ($this->achats->removeElement($achat)) {
            // set the owning side to null (unless already changed)
            if ($achat->getLeUtilisateur() === $this) {
                $achat->setLeUtilisateur(null);
            }
        }

        return $this;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername()
    {
        $this->username = $this->nom." ".$this->prenom;

	    return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getRoles()
    {
        return $this->GetRoleUser();
    }

    public function getSalt()
    {

    }

    public function eraseCredentials()
    {

    }

    public function __toString()
    {
        return strval($this->id);
    }


}
