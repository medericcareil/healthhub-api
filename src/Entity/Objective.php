<?php

namespace App\Entity;

use Symfony\Component\Uid\Uuid;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\UuidV4;
use App\Repository\ObjectiveRepository;

#[ORM\Entity(repositoryClass: ObjectiveRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Objective
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private UuidV4 $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'objectives')]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    #[ORM\ManyToOne(targetEntity: ObjectiveType::class, inversedBy: 'objectives')]
    #[ORM\JoinColumn(nullable: false)]
    private ObjectiveType $objective_type;

    #[ORM\Column(type: 'json')]
    private array $value = [];

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $created_at;

    public function __construct()
    {
        $this->id = Uuid::v4();
    }

    public function getId(): ?UuidV4
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getObjectiveType(): ?ObjectiveType
    {
        return $this->objective_type;
    }

    public function setObjectiveType(?ObjectiveType $objective_type): self
    {
        $this->objective_type = $objective_type;

        return $this;
    }

    public function getValue(): ?array
    {
        return $this->value;
    }

    public function setValue(array $value): self
    {
        $this->value = $value;

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

    /**
     * Returns the Objective's infos as an array
     * @param Objective $objective 
     * @return array 
     */
    public static function toArray(Objective $objective): array
    {
        return [
            'id'                => $objective->getId(),
            'user_id'           => $objective->getUser()->getId(),
            'objective_type_id' => $objective->getObjectiveType()->getId(),
            'value'             => $objective->getValue(),
            'created_at'        => $objective->getCreatedAt(),
        ];
    }

    /**
     * Returns an array of the Objective's infos as an array
     * @param Objective[] $objectives
     * @return array 
     */
    public static function toArrays(mixed $objectives): array
    {
        $data = [];
        foreach ($objectives as $key => $objective) {
            $data[$key]['id']                = $objective->getId();
            $data[$key]['user_id']           = $objective->getUser()->getId();
            $data[$key]['objective_type_id'] = $objective->getObjectiveType()->getId();
            $data[$key]['value']             = $objective->getValue();
            $data[$key]['created_at']        = $objective->getCreatedAt();
        }

        return $data;
    }
}
