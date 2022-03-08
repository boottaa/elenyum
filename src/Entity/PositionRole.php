<?php

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PositionRoleRepository")
 */
class PositionRole
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\OneToOne(targetEntity=Position::class, inversedBy="positionRole")
     * @ORM\JoinColumn(nullable=false)
     */
    private Position $position;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $roles;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private DateTimeImmutable $createdAt;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $updatedAt;

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

    public function getRoles(): array
    {
        if (isset($this->roles)) {
            $roles = explode('.', $this->roles);
            return array_map(function ($item) {
                // For is granted
                return $item;
            }, $roles);
        }

        return [];
    }

    public function addRole(int $roleId): self
    {
        $roles = $this->getRoles();

        if (!in_array($roleId, $roles, false)) {
            $roles[] = $roleId;
        }

        $this->roles = implode('.', $roles);

        return $this;
    }

    public function removeRole(int $roleId): self
    {
        $roles = $this->getRoles();

        if ($key = array_search($roleId, $roles, false)) {
            unset($roles[$key]);
        }

        $this->roles = implode('.', $roles);

        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
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
