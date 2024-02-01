<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\MemberRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=MemberRepository::class)
 */
class Member
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("member")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups("member")
     */
    private $avatar_url;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Ce champs ne peut pas être vide")
     * @Assert\Length(max=255, maxMessage="Le nom de la rue ne peut pas dépasser {{ limit }} caractères")
     * @Groups("member")
     */
    private $street_name;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     * @Assert\Type(type="integer", message="Ce champs doit être un entier")
     * @Groups("member")
     */
    private $street_number;

    /**
     * @ORM\Column(type="string", length=10)
     * @Assert\Regex(pattern="/^[0-9]+$/", message="Ce champs ne doit contenir que des nombres")
     * @Assert\NotBlank(message="Ce champs ne peut pas être vide")
     */
    private $zip_code;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank(message="Ce champs ne peut pas être vide")
     * @Assert\Length(max=100, maxMessage="Le nom de la ville ne peut pas dépasser {{ limit }} caractères")
     * @Groups("member")
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank(message="Ce champs ne peut pas être vide")
     * @Assert\Length(max=50, maxMessage="Le nom du pays ne peut pas dépasser {{ limit }} caractères")
     * @Groups("member")
     */
    private $country;

    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\Length(max=20, maxMessage="Le numéro de téléphone ne peut pas dépasser {{ limit }} caractères")
     * @Assert\Regex(pattern="/^[0-9]+$/", message="Ce champs ne doit contenir que des nombres")
     * @Assert\NotBlank(message="Ce champs ne peut pas être vide")
     * @Groups("member")
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank(message="Ce champs ne peut pas être vide")
     * @Assert\Length(max=50, maxMessage="Le nom de la fonction ne peut pas dépasser {{ limit }} caractères")
     * @Groups("member")
     */
    private $function;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string The hashed password
     */
    private $password;

    /**
     * @ORM\Column(type="boolean", options={"default": "0"})
     * @Groups("member")
     */
    private $membership_statut;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups("member")
     */
    private $membership_expiration;

    /**
     * @ORM\OneToOne(targetEntity=User::class, mappedBy="member", cascade={"persist", "remove"})
     * @Groups("member")
     */
    private $user;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $roles = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAvatarUrl(): ?string
    {
        return $this->avatar_url;
    }

    public function setAvatarUrl(?string $avatar_url): self
    {
        $this->avatar_url = $avatar_url;

        return $this;
    }

    public function getStreetName(): ?string
    {
        return $this->street_name;
    }

    public function setStreetName(string $street_name): self
    {
        $this->street_name = $street_name;

        return $this;
    }

    public function getStreetNumber(): ?int
    {
        return $this->street_number;
    }

    public function setStreetNumber(?int $street_number): self
    {
        $this->street_number = $street_number;

        return $this;
    }

    public function getZipCode(): ?string
    {
        return $this->zip_code;
    }

    public function setZipCode(string $zip_code): self
    {
        $this->zip_code = $zip_code;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getFunction(): ?string
    {
        return $this->function;
    }

    public function setFunction(string $function): self
    {
        $this->function = $function;

        return $this;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function isMembershipStatut(): ?bool
    {
        return $this->membership_statut;
    }

    public function setMembershipStatut(bool $membership_statut): self
    {
        $this->membership_statut = $membership_statut;

        return $this;
    }

    public function getMembershipExpiration(): ?\DateTimeInterface
    {
        return $this->membership_expiration;
    }

    public function setMembershipExpiration(\DateTimeInterface $membership_expiration): self
    {
        $this->membership_expiration = $membership_expiration;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        // unset the owning side of the relation if necessary
        if ($user === null && $this->user !== null) {
            $this->user->setMember(null);
        }

        // set the owning side of the relation if necessary
        if ($user !== null && $user->getMember() !== $this) {
            $user->setMember($this);
        }

        $this->user = $user;

        return $this;
    }

    public function getRoles(): ?array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(?array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }
}
