<?php

namespace App\Entity;

use App\Repository\LocationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Адрес точки нужен для доставки товаров (идея в будущем осуществлять доставку не достающих товаров по адресам)
 *
 * @ORM\Entity(repositoryClass=LocationRepository::class)
 */
class Location
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $address;

    /**
     * @ORM\OneToOne(targetEntity=Branch::class, mappedBy="location")
     * @ORM\JoinColumn(nullable=false)
     */
    private Branch $branch;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return Branch
     */
    public function getBranch(): Branch
    {
        return $this->branch;
    }

    /**
     * @param Branch $branch
     * @return $this
     */
    public function setBranch(Branch $branch): self
    {
        $this->branch = $branch;

        return $this;
    }
}
