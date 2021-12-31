<?php

namespace App\Entity;

use App\Repository\SheduleOperationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SheduleOperationRepository::class)
 */
class SheduleOperation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */

    private int $id;

    /**
     * @ORM\ManyToOne(targetEntity=Operation::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private Operation $operation;

    /**
     * Количество операций
     *
     * @ORM\Column(type="integer")
     */

    private int $count;

    /**
     * @ORM\ManyToOne(targetEntity=Shedule::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private Shedule $shedule;

    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return Operation
     */
    public function getOperation(): Operation
    {
        return $this->operation;
    }

    /**
     * @param Operation $operation
     * @return SheduleOperation
     */
    public function setOperation(Operation $operation): SheduleOperation
    {
        $this->operation = $operation;

        return $this;
    }

    /**
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * @param int $count
     * @return SheduleOperation
     */
    public function setCount(int $count): SheduleOperation
    {
        $this->count = $count;

        return $this;
    }

    public function getShedule(): Shedule
    {
        return $this->shedule;
    }

    public function setShedule(Shedule $shedule): self
    {
        $this->shedule = $shedule;

        return $this;
    }
}
