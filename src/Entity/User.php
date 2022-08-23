<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\UuidV4;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\HasLifecycleCallbacks]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private UuidV4 $id;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private string $email;

    #[ORM\Column(type: 'json')]
    private $roles = [];

    #[ORM\Column(type: 'string')]
    private string $password;

    #[ORM\Column(type: 'string', length: 50)]
    private string $pseudo;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $image;

    #[ORM\Column(type: 'boolean')]
    private bool $gender;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $birthdate;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $token;

    #[ORM\Column(type: 'boolean')]
    private bool $is_validated = false;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $created_at;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $updated_at = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserCharacteristic::class, orphanRemoval: true)]
    private $userCharacteristics;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Activity::class, orphanRemoval: true)]
    private $activities;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Objective::class, orphanRemoval: true)]
    private $objectives;

    public function __construct()
    {
        $this->id = Uuid::v4();
        $this->userCharacteristics = new ArrayCollection();
        $this->activities = new ArrayCollection();
        $this->objectives = new ArrayCollection();
    }

    public function getId(): ?UuidV4
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): self
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getGender(): ?bool
    {
        return $this->gender;
    }

    public function setGender(bool $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function getBirthdate(): ?\DateTimeImmutable
    {
        return $this->birthdate;
    }

    public function setBirthdate(\DateTimeImmutable $birthdate): self
    {
        $this->birthdate = $birthdate;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getIsValidated(): bool
    {
        return $this->is_validated;
    }

    public function setIsValidated(bool $is_validated): self
    {
        $this->is_validated = $is_validated;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    #[ORM\PrePersist]
    public function setCreatedAt(): self
    {
        if (!isset($this->created_at)) {
            $this->created_at = new \DateTimeImmutable('now');
        }
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    #[ORM\PreUpdate]
    public function setUpdatedAt(): self
    {
        $this->updated_at = new \DateTime('now');

        return $this;
    }

    /**
     * Returns a User form an array
     * @param array $array 
     * @return User 
     */
    public static function fromArray(array $array): User
    {
        return (new User())
            ->setEmail($array['email'])
            ->setPseudo($array['pseudo'])
            ->setPassword($array['password'])
            ->setRoles($array['roles'] ?? ['ROLE_USER'])
            ->setImage($array['image'] ?? null)
            ->setGender($array['gender'])
            ->setBirthdate($array['birthdate'])
            ->setIsValidated($array['is_validated']);
    }

    /**
     * Returns the User's infos as an array
     * @param User $user 
     * @return array 
     */
    public static function toArray(User $user): array
    {
        return [
            'id'         => $user->getId(),
            'email'      => $user->getEmail(),
            'pseudo'     => $user->getPseudo(),
            'roles'      => $user->getRoles(),
            'image'      => $user->getImage(),
            'gender'     => $user->getGender(),
            'birthdate'  => $user->getBirthdate(),
            'created_at' => $user->getCreatedAt(),
            'updated_at' => $user->getUpdatedAt(),
        ];
    }

    /**
     * @return Collection<int, UserCharacteristic>
     */
    public function getUserCharacteristics(): Collection
    {
        return $this->userCharacteristics;
    }

    public function addUserCharacteristic(UserCharacteristic $userCharacteristic): self
    {
        if (!$this->userCharacteristics->contains($userCharacteristic)) {
            $this->userCharacteristics[] = $userCharacteristic;
            $userCharacteristic->setUser($this);
        }

        return $this;
    }

    public function removeUserCharacteristic(UserCharacteristic $userCharacteristic): self
    {
        if ($this->userCharacteristics->removeElement($userCharacteristic)) {
            // set the owning side to null (unless already changed)
            if ($userCharacteristic->getUser() === $this) {
                $userCharacteristic->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Activity>
     */
    public function getActivities(): Collection
    {
        return $this->activities;
    }

    public function addActivity(Activity $activity): self
    {
        if (!$this->activities->contains($activity)) {
            $this->activities[] = $activity;
            $activity->setUser($this);
        }

        return $this;
    }

    public function removeActivity(Activity $activity): self
    {
        if ($this->activities->removeElement($activity)) {
            // set the owning side to null (unless already changed)
            if ($activity->getUser() === $this) {
                $activity->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Objective>
     */
    public function getObjectives(): Collection
    {
        return $this->objectives;
    }

    public function addObjective(Objective $objective): self
    {
        if (!$this->objectives->contains($objective)) {
            $this->objectives[] = $objective;
            $objective->setUser($this);
        }

        return $this;
    }

    public function removeObjective(Objective $objective): self
    {
        if ($this->objectives->removeElement($objective)) {
            // set the owning side to null (unless already changed)
            if ($objective->getUser() === $this) {
                $objective->setUser(null);
            }
        }

        return $this;
    }
}
