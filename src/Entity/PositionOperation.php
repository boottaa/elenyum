<?php

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class PositionOperation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\ManyToOne(targetEntity=Position::class, inversedBy="positionOperation")
     * @ORM\JoinColumn(nullable=false)
     */
    private Position $position;

    /**
     * @ORM\ManyToOne(targetEntity=Operation::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private Operation $operation;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private DateTimeImmutable $createdAt;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private ?DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->setCreatedAt(new DateTimeImmutable());
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getPosition(): Position
    {
        return $this->position;
    }

    public function setPosition(Position $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getOperation(): Operation
    {
        return $this->operation;
    }

    public function setOperation(Operation $operation): self
    {
        $this->operation = $operation;

        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    private function setCreatedAt(DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
