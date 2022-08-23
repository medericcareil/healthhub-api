<?php

namespace App\Entity;

use Symfony\Component\Uid\Uuid;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\UuidV4;
use App\Repository\ActivityTypeRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: ActivityTypeRepository::class)]
#[ORM\HasLifecycleCallbacks]
class ActivityType
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

    #[ORM\OneToMany(mappedBy: 'activity_type', targetEntity: Activity::class, orphanRemoval: true)]
    private Collection $activities;

    public function __construct()
    {
        $this->id = Uuid::v4();
        $this->activities = new ArrayCollection();
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
            $activity->setActivityType($this);
        }

        return $this;
    }

    public function removeActivity(Activity $activity): self
    {
        if ($this->activities->removeElement($activity)) {
            // set the owning side to null (unless already changed)
            if ($activity->getActivityType() === $this) {
                $activity->setActivityType(null);
            }
        }

        return $this;
    }

    /**
     * Returns a ActivityType form an array
     * @param array $array 
     * @return ActivityType 
     */
    public static function fromArray(array $array): ActivityType
    {
        return (new ActivityType())
            ->setName($array['name_type']);
    }

    /**
     * Returns the ActivityType's infos as an array
     * @param ActivityType $activityType 
     * @return array 
     */
    public static function toArray(ActivityType $activityType): array
    {
        return [
            'id'         => $activityType->getId(),
            'name'       => $activityType->getName(),
            'created_at' => $activityType->getCreatedAt(),
            'updated_at' => $activityType->getUpdatedAt(),
        ];
    }

    /**
     * Returns an array of the ActivityType's infos as an array
     * @param ActivityType[] $activitiesTypes
     * @return array 
     */
    public static function toArrays(mixed $activitiesTypes): array
    {
        $data = [];
        foreach ($activitiesTypes as $key => $activityType) {
            $data[$key]['id']         = $activityType->getId();
            $data[$key]['name']       = $activityType->getName();
            $data[$key]['created_at'] = $activityType->getCreatedAt();
            $data[$key]['updated_at'] = $activityType->getUpdatedAt();
        }

        return $data;
    }
}
