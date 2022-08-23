<?php

namespace App\Entity;

use Symfony\Component\Uid\Uuid;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\UuidV4;
use App\Repository\ObjectiveTypeRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: ObjectiveTypeRepository::class)]
#[ORM\HasLifecycleCallbacks]
class ObjectiveType
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private UuidV4 $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $created_at;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $updated_at = null;

    #[ORM\OneToMany(mappedBy: 'objective_type', targetEntity: Objective::class, orphanRemoval: true)]
    private Collection $objectives;

    public function __construct()
    {
        $this->id = Uuid::v4();
        $this->objectives = new ArrayCollection();
    }

    public function getId(): ?UuidV4
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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
            $objective->setObjectiveType($this);
        }

        return $this;
    }

    public function removeObjective(Objective $objective): self
    {
        if ($this->objectives->removeElement($objective)) {
            // set the owning side to null (unless already changed)
            if ($objective->getObjectiveType() === $this) {
                $objective->setObjectiveType(null);
            }
        }

        return $this;
    }

    /**
     * Returns a ObjectiveType form an array
     * @param array $array 
     * @return ObjectiveType 
     */
    public static function fromArray(array $array): ObjectiveType
    {
        return (new ObjectiveType())
            ->setName($array['name_type']);
    }

    /**
     * Returns the ObjectiveType's infos as an array
     * @param ObjectiveType $objectiveType 
     * @return array 
     */
    public static function toArray(ObjectiveType $objectiveType): array
    {
        return [
            'id'         => $objectiveType->getId(),
            'name'       => $objectiveType->getName(),
            'created_at' => $objectiveType->getCreatedAt(),
            'updated_at' => $objectiveType->getUpdatedAt(),
        ];
    }

    /**
     * Returns an array of the ObjectiveType's infos as an array
     * @param ObjectiveType[] $objectivesTypes
     * @return array 
     */
    public static function toArrays(mixed $objectivesTypes): array
    {
        $data = [];
        foreach ($objectivesTypes as $key => $objectiveType) {
            $data[$key]['id']         = $objectiveType->getId();
            $data[$key]['name']       = $objectiveType->getName();
            $data[$key]['created_at'] = $objectiveType->getCreatedAt();
            $data[$key]['updated_at'] = $objectiveType->getUpdatedAt();
        }

        return $data;
    }
}
