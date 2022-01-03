<?php

namespace App\Entity;

use App\Repository\PositionRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PositionRepository::class)
 */
class Position
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity=Employee::class, mappedBy="position")
     */
    private Collection $employee;

    /**
     * @ORM\OneToOne(targetEntity=PositionRole::class, mappedBy="employee")
     */
    private PositionRole $positionRole;

    /**
     * @ORM\Column(type="boolean")
     */
    private $inCalendar;

    public function __construct()
    {
        $this->employee = new ArrayCollection();
        $this->setCreatedAt(new DateTimeImmutable());
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

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

    /**
     * @return Collection|Employee[]
     */
    public function getEmployee(): Collection
    {
        return $this->employee;
    }

    public function addEmployee(Employee $employee): self
    {
        if (!$this->employee->contains($employee)) {
            $this->employee[] = $employee;
            $employee->setPosition($this);
        }

        return $this;
    }

    public function removeEmployee(Employee $employee): self
    {
        if ($this->employee->removeElement($employee)) {
            // set the owning side to null (unless already changed)
            if ($employee->getPosition() === $this) {
                $employee->setPosition(null);
            }
        }

        return $this;
    }

    public function getInCalendar(): ?bool
    {
        return $this->inCalendar;
    }

    public function setInCalendar(bool $inCalendar): self
    {
        $this->inCalendar = $inCalendar;

        return $this;
    }

    /**
     * @return PositionRole
     */
    public function getPositionRole(): PositionRole
    {
        return $this->positionRole;
    }

    /**
     * @param PositionRole $positionRole
     * @return Position
     */
    public function setPositionRole(PositionRole $positionRole): Position
    {
        $positionRole->setPosition($this);
        $this->positionRole = $positionRole;

        return $this;
    }
}
