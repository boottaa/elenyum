<?php

namespace App\Entity;

use App\Repository\TechnologicalMapRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TechnologicalMapRepository::class)
 */
class TechnologicalMap
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
     * @ORM\Column(type="text", nullable=true)
     */
    
    private $comment;

    /**
     * @ORM\ManyToMany(targetEntity=Commodity::class)
     */
    
    private $consumables;

    public function __construct()
    {
        $this->consumables = new ArrayCollection();
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

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * @return Collection|Commodity[]
     */
    public function getConsumables(): Collection
    {
        return $this->consumables;
    }

    public function addConsumable(Commodity $consumable): self
    {
        if (!$this->consumables->contains($consumable)) {
            $this->consumables[] = $consumable;
        }

        return $this;
    }

    public function removeConsumable(Commodity $consumable): self
    {
        $this->consumables->removeElement($consumable);

        return $this;
    }
}
