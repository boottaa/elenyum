<?php

namespace App\Entity;

use App\Repository\BranchRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

/**
 * Филиалы компании
 *
 * @ORM\Entity(repositoryClass=BranchRepository::class)
 */
class Branch implements JsonSerializable
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
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity=Company::class, inversedBy="branches")
     * @ORM\JoinColumn(nullable=false)
     */
    private Company $company;

    /**
     * @ORM\OneToMany(targetEntity=Employee::class, mappedBy="branch", orphanRemoval=true)
     */
    private Collection $employees;

    /**
     * @ORM\OneToOne(targetEntity=Location::class, mappedBy="branch")
     * @ORM\JoinColumn(nullable=false)
     */
    private Location $location;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private ?DateTimeImmutable $start;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private ?DateTimeImmutable $end;

    public function __construct()
    {
        $this->employees = new ArrayCollection();
    }

    public function getId(): ?int
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

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): self
    {
        $this->company = $company;

        return $this;
    }

    public function addEmployee(Employee $employee): self
    {
        if (!$this->employees->contains($employee)) {
            $this->employees[] = $employee;
            $employee->setCompany($this);
        }

        return $this;
    }

    public function removeEmployee(Employee $employee): self
    {
        if ($this->employees->removeElement($employee)) {
            // set the owning side to null (unless already changed)
            if ($employee->getCompany() === $this) {
                $employee->setCompany(null);
            }
        }

        return $this;
    }

    /**
     * @return Location
     */
    public function getLocation(): Location
    {
        return $this->location;
    }

    /**
     * @param Location $location
     */
    public function setLocation(Location $location): void
    {
        $this->location = $location;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getStart(): ?DateTimeImmutable
    {
        return $this->start;
    }

    /**
     * @param DateTimeImmutable|null $start
     * @return Branch
     */
    public function setStart(?DateTimeImmutable $start): Branch
    {
        $this->start = $start;

        return $this;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getEnd(): ?DateTimeImmutable
    {
        return $this->end;
    }

    /**
     * @param DateTimeImmutable|null $end
     * @return Branch
     */
    public function setEnd(?DateTimeImmutable $end): Branch
    {
        $this->end = $end;

        return $this;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'start' => $this->getStart(),
            'end' => $this->getEnd(),
            'address' => $this->getLocation()->getAddress(),
        ];
    }
}
