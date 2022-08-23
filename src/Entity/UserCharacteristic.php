<?php

namespace App\Entity;

use Symfony\Component\Uid\Uuid;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\UuidV4;
use App\Repository\UserCharacteristicRepository;

#[ORM\Entity(repositoryClass: UserCharacteristicRepository::class)]
#[ORM\HasLifecycleCallbacks]
class UserCharacteristic
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private UuidV4 $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'userCharacteristics')]
    #[ORM\JoinColumn(nullable: false)]
    private $user;

    #[ORM\Column(type: 'float', nullable: true)]
    private $weight;

    #[ORM\Column(type: 'float', nullable: true)]
    private $height;

    #[ORM\Column(type: 'datetime_immutable')]
    private $created_at;

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

    public function getWeight(): ?float
    {
        return $this->weight;
    }

    public function setWeight(?float $weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    public function getHeight(): ?float
    {
        return $this->height;
    }

    public function setHeight(?float $height): self
    {
        $this->height = $height;

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
     * Returns a UserCharacteristic form an array
     * @param array $array 
     * @return UserCharacteristic
     */
    public static function fromArray(array $array): UserCharacteristic
    {
        return (new UserCharacteristic())
            ->setUser($array['user_id'])
            ->setWeight($array['weight'])
            ->setHeight($array['height']);
    }

    /**
     * Returns the UserCharacteristic's infos as an array
     * @param UserCharacteristic $userCharacteristic 
     * @return array 
     */
    public static function toArray(UserCharacteristic $userCharacteristic): array
    {
        return [
            'id'         => $userCharacteristic->getId(),
            'user_id'    => $userCharacteristic->getUser()->getId(),
            'weigth'     => $userCharacteristic->getWeight(),
            'height'     => $userCharacteristic->getHeight(),
            'created_at' => $userCharacteristic->getCreatedAt(),
        ];
    }

    /**
     * Returns an array of the UserCharacteristic's infos as an array
     * @param UserCharacteristic[] $userCharacteristic 
     * @return array 
     */
    public static function toArrays(mixed $userCharacteristic): array
    {
        $data = [];
        foreach ($userCharacteristic as $key => $characteristic) {
            $data[$key]['id']         = $characteristic->getId();
            $data[$key]['user_id']    = $characteristic->getUser()->getId();
            $data[$key]['weigth']     = $characteristic->getWeight();
            $data[$key]['height']     = $characteristic->getHeight();
            $data[$key]['created_at'] = $characteristic->getCreatedAt();
        }

        return $data;
    }
}
