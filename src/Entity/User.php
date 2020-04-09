<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert ;


/**

 * @ApiResource(
 * collectionOperations={
 *     "usercreate"={
 *          "method"="post",
 *          "controller"= "App\Controller\UserCreateController",
 *     }
 *  },
 *  attributes={ "normalizationContext"={"groups"={"read"}},
 *          "denormalizationContext"={"groups" = {"write"}}
 *     }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 *
 * 
 * 
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Groups({"read", "write"})
     * @Assert\NotNull(message="ce champ ne doit pas être null")
     *
     */

    private $username;

    /**
     * @ORM\Column(type="json")
     * @Groups({"read"})
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Groups({"read", "write"})
     * @Assert\NotBlank(message="ce champ ne doit pas être null")
     */
    private $password;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"read", "write"})
     *
     */
    private $isActif;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Partenaire", inversedBy="user")
     *
     */
    private $partenaire;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Compte", mappedBy="userCreator")
     */
    private $comptes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Depot", mappedBy="userwhodid")
     */
    private $depots;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Profil", inversedBy="user")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"read", "write"})
     * @Assert\NotBlank(message="ce champ ne doit pas être null")
     */
    private $profil;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Partenaire", mappedBy="utilisateur")
     */
    private $partenaires;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Compte", inversedBy="users")
     */
    private $account;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Transaction", mappedBy="user")
     */
    private $userEmetteur;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Transaction", mappedBy="userR")
     */
    private $userRecepteur;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Affectation", mappedBy="user")
     */
    private $affectations;

    public function __construct()
    {
        $this->comptes = new ArrayCollection();
        $this->depots = new ArrayCollection();
        $this->partenaires = new ArrayCollection();
        $this->userEmetteur = new ArrayCollection();
        $this->userRecepteur = new ArrayCollection();
        $this->affectations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        //$this->plainPassword = null;
    }

    public function getIsActif(): ?bool
    {
        return $this->isActif;
    }

    public function setIsActif(bool $is_actif): self
    {
        $this->isActif = $is_actif;

        return $this;
    }

    public function getPartenaire(): ?Partenaire
    {
        return $this->partenaire;
    }

    public function setPartenaire(?Partenaire $partenaire): self
    {
        $this->partenaire = $partenaire;

        return $this;
    }

    /**
     * @return Collection|Compte[]
     */
    public function getComptes(): Collection
    {
        return $this->comptes;
    }

    public function addCompte(Compte $compte): self
    {
        if (!$this->comptes->contains($compte)) {
            $this->comptes[] = $compte;
            $compte->setUserCreator($this);
        }

        return $this;
    }

    public function removeCompte(Compte $compte): self
    {
        if ($this->comptes->contains($compte)) {
            $this->comptes->removeElement($compte);
            // set the owning side to null (unless already changed)
            if ($compte->getUserCreator() === $this) {
                $compte->setUserCreator(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Depot[]
     */
    public function getDepots(): Collection
    {
        return $this->depots;
    }

    public function addDepot(Depot $depot): self
    {
        if (!$this->depots->contains($depot)) {
            $this->depots[] = $depot;
            $depot->setUserwhodid($this);
        }

        return $this;
    }

    public function removeDepot(Depot $depot): self
    {
        if ($this->depots->contains($depot)) {
            $this->depots->removeElement($depot);
            // set the owning side to null (unless already changed)
            if ($depot->getUserwhodid() === $this) {
                $depot->setUserwhodid(null);
            }
        }

        return $this;
    }

    public function getProfil(): ?Profil
    {
        return $this->profil;
    }

    public function setProfil(?Profil $profil): self
    {
        $this->profil = $profil;

        return $this;
    }

  /**
    * @return Collection|Partenaire[]
    */
   public function getPartenaires(): Collection
  {
        return $this->partenaires;
   }

   public function addPartenaire(Partenaire $partenaire): self
    {
        if (!$this->partenaires->contains($partenaire)) {
            $this->partenaires[] = $partenaire;
            $partenaire->setUtilisateur($this);
        }

        return $this;
    }

    public function removePartenaire(Partenaire $partenaire): self{
        if ($this->partenaires->contains($partenaire)) {
            $this->partenaires->removeElement($partenaire);
            // set the owning side to null (unless already changed)
            if ($partenaire->getUtilisateur() === $this) {
                $partenaire->setUtilisateur(null);
            }
        }

        return $this;
    }

    public function getAccount(): ?Compte
    {
        return $this->account;
    }

    public function setAccount(?Compte $account): self
    {
        $this->account = $account;

        return $this;
    }

    /**
     * @return Collection|Transaction[]
     */
    public function getUserEmetteur(): Collection
    {
        return $this->userEmetteur;
    }

    public function addUserEmetteur(Transaction $userEmetteur): self
    {
        if (!$this->userEmetteur->contains($userEmetteur)) {
            $this->userEmetteur[] = $userEmetteur;
            $userEmetteur->setUser($this);
        }

        return $this;
    }

    public function removeUserEmetteur(Transaction $userEmetteur): self
    {
        if ($this->userEmetteur->contains($userEmetteur)) {
            $this->userEmetteur->removeElement($userEmetteur);
            // set the owning side to null (unless already changed)
            if ($userEmetteur->getUser() === $this) {
                $userEmetteur->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Transaction[]
     */
    public function getUserRecepteur(): Collection
    {
        return $this->userRecepteur;
    }

    public function addUserRecepteur(Transaction $userRecepteur): self
    {
        if (!$this->userRecepteur->contains($userRecepteur)) {
            $this->userRecepteur[] = $userRecepteur;
            $userRecepteur->setUserR($this);
        }

        return $this;
    }

    public function removeUserRecepteur(Transaction $userRecepteur): self
    {
        if ($this->userRecepteur->contains($userRecepteur)) {
            $this->userRecepteur->removeElement($userRecepteur);
            // set the owning side to null (unless already changed)
            if ($userRecepteur->getUserR() === $this) {
                $userRecepteur->setUserR(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Affectation[]
     */
    public function getAffectations(): Collection
    {
        return $this->affectations;
    }

    public function addAffectation(Affectation $affectation): self
    {
        if (!$this->affectations->contains($affectation)) {
            $this->affectations[] = $affectation;
            $affectation->setUser($this);
        }

        return $this;
    }

    public function removeAffectation(Affectation $affectation): self
    {
        if ($this->affectations->contains($affectation)) {
            $this->affectations->removeElement($affectation);
            // set the owning side to null (unless already changed)
            if ($affectation->getUser() === $this) {
                $affectation->setUser(null);
            }
        }

        return $this;
    }
}
