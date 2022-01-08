<?php

namespace App\Entity;

use App\Repository\RoleRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

/**
 * @ORM\Entity(repositoryClass=RoleRepository::class)
 */
class Role implements JsonSerializable
{
    public const SHEDULE_POST = 1;
    public const SHEDULE_DELETE = 2;
    public const SHEDULE_GET = 3;
    public const OPERATION_POST = 4;
    public const OPERATION_DELETE = 5;
    public const OPERATION_GET = 6;
    public const CLIENT_POST = 7;
    public const CLIENT_DELETE = 8;
    public const CLIENT_GET = 9;
    public const EMPLOYEE_POST = 10;
    public const EMPLOYEE_DELETE = 11;
    public const EMPLOYEE_GET = 12;
    public const BRANCH_POST = 16;
    public const BRANCH_DELETE = 17;
    public const BRANCH_GET = 18;
    public const COMMODITY_POST = 19;
    public const COMMODITY_DELETE = 20;
    public const COMMODITY_GET = 21;
    public const COMPANY_POST = 22;
    public const COMPANY_DELETE = 23;
    public const COMPANY_GET = 24;
    public const TECHNOLOGICAL_MAP_POST = 25;
    public const TECHNOLOGICAL_MAP_DELETE = 26;
    public const TECHNOLOGICAL_MAP_GET = 27;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $title;

    /**
     * @ORM\Column(type="string", length=400)
     */
    private string $description;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private DateTimeImmutable $createdAt;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $this->setCreatedAt(new DateTimeImmutable());
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return Role
     */
    public function setDescription(string $description): Role
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'title' => $this->getTitle(),
            'description' => $this->getDescription(),
        ];
    }
}
