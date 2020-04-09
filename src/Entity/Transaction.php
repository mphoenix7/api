<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use App\Controller\TransactionController;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert ;

/**
 * @ApiResource(collectionOperations={
 *     "transaction"={
 *          "method"="post",
 *          "controller"= TransactionController::class
 *     }
 *  },
 *  normalizationContext={"groups"={"read"}},
 *          denormalizationContext={"groups" = {"write"}}
 *     )
 * @ORM\Entity(repositoryClass="App\Repository\TransactionRepository")
 */
class Transaction
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string" , length=255)
     * @Groups({"read" ,"write"})
     */
    private $code;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"read", "write"})
     * @Assert\NotNull(message="Ce champs est obligatoire")
     */
    private $montant;

    /**
     * @ORM\Column(type="integer")
     */
    private $frais;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"read", "write"})
     * @Assert\NotNull(message="Ce champs est obligatoire")
     *
     */
    private $emetteur;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"read", "write"})
     * @Assert\NotNull(message="Ce champs est obligatoire")
     */
    private $typePieceEmetteur;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"read", "write"})
     * @Assert\NotNull(message="Ce champs est obligatoire")
     */
    private $numeroPieceEmetteur;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateEnvoie;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"read", "write"})
     * @Assert\NotNull(message="Ce champs est obligatoire")
     */
    private $telephoneEmetteur;

    /**
     * @ORM\Column(type="float")
     */
    private $commissionEmetteur;

    /**
     * @ORM\Column(type="datetime" , nullable=true)
     * @Groups({"read", "write"})
     */
    private $dateRetrait;

    /**
     * @ORM\Column(type="string", length=255 , )
     * @Groups({"read", "write"})
     */
    private $Recepteur;

    /**
     * @ORM\Column(type="string", length=255 , nullable=true)
     * @Groups({"read", "write"})
     *
     */
    private $typePieceRecepteur;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"read", "write"})
     * @Assert\NotNull(message="Ce champs est obligatoire")
     */
    private $telephoneRecepteur;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"read", "write"})
     */
    private $numeroPieceRecepteur;

    /**
     * @ORM\Column(type="float" )
     *
     */
    private $commissionRecepteur;

    /**
     * @ORM\Column(type="float")
     */
    private $commissionSysteme;

    /**
     * @ORM\Column(type="float")
     */
    private $taxeEtats;

    /**
     * @ORM\Column(type="boolean")
     */
    private $statu;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="userEmetteur")
     * @ORM\JoinColumn(nullable=false)
     */
    private $userD;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="userRecepteur")
     * @ORM\JoinColumn(nullable=true)
     */
    private $userR;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Compte", inversedBy="transactionE")
     * @ORM\JoinColumn(nullable=false)
     */
    private $compteD;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Compte", inversedBy="compteR")
     * @ORM\JoinColumn(nullable=true)
     */
    private $compteR;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Tarif", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $tarif;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getMontant(): ?int
    {
        return $this->montant;
    }

    public function setMontant(int $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    public function getFrais(): ?int
    {
        return $this->frais;
    }

    public function setFrais(int $frais): self
    {
        $this->frais = $frais;

        return $this;
    }

    public function getEmetteur(): ?string
    {
        return $this->emetteur;
    }

    public function setEmetteur(string $emetteur): self
    {
        $this->emetteur = $emetteur;

        return $this;
    }

    public function getTypePieceEmetteur(): ?string
    {
        return $this->typePieceEmetteur;
    }

    public function setTypePieceEmetteur(string $typePieceEmetteur): self
    {
        $this->typePieceEmetteur = $typePieceEmetteur;

        return $this;
    }

    public function getNumeroPieceEmetteur(): ?string
    {
        return $this->numeroPieceEmetteur;
    }

    public function setNumeroPieceEmetteur(string $numeroPieceEmetteur): self
    {
        $this->numeroPieceEmetteur = $numeroPieceEmetteur;

        return $this;
    }

    public function getDateEnvoie(): ?\DateTimeInterface
    {
        return $this->dateEnvoie;
    }

    public function setDateEnvoie(\DateTimeInterface $dateEnvoie): self
    {
        $this->dateEnvoie = $dateEnvoie;

        return $this;
    }

    public function getTelephoneEmetteur(): ?string
    {
        return $this->telephoneEmetteur;
    }

    public function setTelephoneEmetteur(string $telephoneEmetteur): self
    {
        $this->telephoneEmetteur = $telephoneEmetteur;

        return $this;
    }

    public function getCommissionEmetteur(): ?float
    {
        return $this->commissionEmetteur;
    }

    public function setCommissionEmetteur(float $commissionEmetteur): self
    {
        $this->commissionEmetteur = $commissionEmetteur;

        return $this;
    }

    public function getDateRetrait(): ?\DateTimeInterface
    {
        return $this->dateRetrait;
    }

    public function setDateRetrait(\DateTimeInterface $dateRetrait): self
    {
        $this->dateRetrait = $dateRetrait;

        return $this;
    }

    public function getRecepteur(): ?string
    {
        return $this->Recepteur;
    }

    public function setRecepteur(string $Recepteur): self
    {
        $this->Recepteur = $Recepteur;

        return $this;
    }

    public function getTypePieceRecepteur(): ?string
    {
        return $this->typePieceRecepteur;
    }

    public function setTypePieceRecepteur(string $typePieceRecepteur): self
    {
        $this->typePieceRecepteur = $typePieceRecepteur;

        return $this;
    }

    public function getTelephoneRecepteur(): ?string
    {
        return $this->telephoneRecepteur;
    }

    public function setTelephoneRecepteur(string $telephoneRecepteur): self
    {
        $this->telephoneRecepteur = $telephoneRecepteur;

        return $this;
    }

    public function getNumeroPieceRecepteur(): ?string
    {
        return $this->numeroPieceRecepteur;
    }

    public function setNumeroPieceRecepteur(string $numeroPieceRecepteur): self
    {
        $this->numeroPieceRecepteur = $numeroPieceRecepteur;

        return $this;
    }

    public function getCommissionRecepteur(): ?float
    {
        return $this->commissionRecepteur;
    }

    public function setCommissionRecepteur(float $commissionRecepteur): self
    {
        $this->commissionRecepteur = $commissionRecepteur;

        return $this;
    }

    public function getCommissionSysteme(): ?float
    {
        return $this->commissionSysteme;
    }

    public function setCommissionSysteme(float $commissionSysteme): self
    {
        $this->commissionSysteme = $commissionSysteme;

        return $this;
    }

    public function getTaxeEtats(): ?float
    {
        return $this->taxeEtats;
    }

    public function setTaxeEtats(float $taxeEtats): self
    {
        $this->taxeEtats = $taxeEtats;

        return $this;
    }

    public function getStatu(): ?bool
    {
        return $this->statu;
    }

    public function setStatu(bool $statu): self
    {
        $this->statu = $statu;

        return $this;
    }

    public function getUserD(): ?User
    {
        return $this->userD;
    }

    public function setUserD(?User $userD): self
    {
        $this->userD = $userD;

        return $this;
    }

    public function getUserR(): ?User
    {
        return $this->userR;
    }

    public function setUserR(?User $userR): self
    {
        $this->userR = $userR;

        return $this;
    }

    public function getCompteD(): ?Compte
    {
        return $this->compteD;
    }

    public function setCompteD(?Compte $compteD): self
    {
        $this->compteD = $compteD;

        return $this;
    }

    public function getCompteR(): ?Compte
    {
        return $this->compteR;
    }

    public function setCompteR(?Compte $compteR): self
    {
        $this->compteR = $compteR;

        return $this;
    }

    public function getTarif(): ?Tarif
    {
        return $this->tarif;
    }

//    public function setTarif(Tarif $tarif): self
//    {
//        $this->tarif = $tarif;
//
//        return $this;
//    }


}
