<?php

namespace App\Entity;

use Symfony\Component\Uid\Uuid;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\UuidV4;
use App\Repository\ActivityRepository;

#[ORM\Entity(repositoryClass: ActivityRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Activity
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private UuidV4 $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'activities')]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    #[ORM\ManyToOne(targetEntity: ActivityType::class, inversedBy: 'activities')]
    #[ORM\JoinColumn(nullable: false)]
    private ActivityType $activity_type;

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

    public function getActivityType(): ?ActivityType
    {
        return $this->activity_type;
    }

    public function setActivityType(?ActivityType $activity_type): self
    {
        $this->activity_type = $activity_type;

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

    // TODO: Delete this after insert fixtures
    public function setCreatedAt(\DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;
     
        return $this;
    }

    // #[ORM\PrePersist]
    // public function setCreatedAt(): self
    // {
    //     if (!isset($this->created_at)) {
    //         $this->created_at = new \DateTimeImmutable('now');
    //     }
    //     return $this;
    // }

    /**
     * Returns the Activity's infos as an array
     * @param Activity $objective 
     * @return array 
     */
    public static function toArray(Activity $activity): array
    {
        return [
            'id'               => $activity->getId(),
            'user_id'          => $activity->getUser()->getId(),
            'activity_type_id' => $activity->getActivityType()->getId(),
            'data'             => $activity->getValue(),
            'created_at'       => $activity->getCreatedAt(),
        ];
    }

    /**
     * Returns an array of the Activity's infos as an array
     * @param Activity[] $activities
     * @return array 
     */
    public static function toArrays(mixed $activities): array
    {
        $data = [];
        foreach ($activities as $key => $activity) {
            $data[$key]['id']               = $activity->getId();
            $data[$key]['user_id']          = $activity->getUser()->getId();
            $data[$key]['activity_type_id'] = $activity->getActivityType()->getId();
            $data[$key]['data']             = $activity->getValue();
            $data[$key]['created_at']       = $activity->getCreatedAt();
        }

        return $data;
    }
}
