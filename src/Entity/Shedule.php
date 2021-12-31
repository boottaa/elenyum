<?php

namespace App\Entity;

use App\Repository\SheduleRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SheduleRepository::class)
 */

class Shedule
{
    public const STATUS_ADDED = 1;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\ManyToOne(targetEntity=Employee::class)
     * @ORM\JoinColumn(nullable=false, referencedColumnName="id")
     */
    private Employee $employee;

    /**
     * @ORM\ManyToOne(targetEntity=Client::class)
     * @ORM\JoinColumn(nullable=false, referencedColumnName="id")
     */
    private Client $client;

    /**
     * @ORM\Column(type="integer")
     */
    private int $status;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $paymentCard;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $paymentCash;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $paymentType;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private DateTimeImmutable $start;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private DateTimeImmutable $end;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private ?DateTimeImmutable $updatedAt;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private ?DateTimeImmutable $createdAt;

    /**
     * @ORM\OneToMany(targetEntity=SheduleOperation::class, mappedBy="shedule")
     */
    private Collection $sheduleOperations;

    public function __construct()
    {
        $this->setCreatedAt(new DateTimeImmutable());
        $this->setStatus(self::STATUS_ADDED);
        $this->sheduleOperations = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getEmployee(): Employee
    {
        return $this->employee;
    }

    public function setEmployee(Employee $employee): self
    {
        $this->employee = $employee;

        return $this;
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function setClient(Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param int $status
     * @return Shedule
     */
    public function setStatus(int $status): Shedule
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getStart(): DateTimeImmutable
    {
        return $this->start;
    }

    /**
     * @param DateTimeImmutable $start
     * @return Shedule
     */
    public function setStart(DateTimeImmutable $start): Shedule
    {
        $this->start = $start;

        return $this;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getEnd(): DateTimeImmutable
    {
        return $this->end;
    }

    /**
     * @param DateTimeImmutable $end
     * @return Shedule
     */
    public function setEnd(DateTimeImmutable $end): Shedule
    {
        $this->end = $end;

        return $this;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * @param DateTimeImmutable|null $updatedAt
     * @return Shedule
     */
    public function setUpdatedAt(?DateTimeImmutable $updatedAt): Shedule
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @param DateTimeImmutable|null $createdAt
     * @return Shedule
     */
    public function setCreatedAt(?DateTimeImmutable $createdAt): Shedule
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection|SheduleOperation[]
     */
    public function getSheduleOperations(): Collection
    {
        return $this->sheduleOperations;
    }

    public function addSheduleOperation(SheduleOperation $sheduleOperation): self
    {
        if (!$this->sheduleOperations->contains($sheduleOperation)) {
            $this->sheduleOperations[] = $sheduleOperation;
            $sheduleOperation->setShedule($this);
        }

        return $this;
    }

    public function removeSheduleOperation(SheduleOperation $sheduleOperation): self
    {
        // set the owning side to null (unless already changed)
        if ($this->sheduleOperations->removeElement($sheduleOperation) && $sheduleOperation->getShedule() === $this) {
            $sheduleOperation->setShedule(null);
        }

        return $this;
    }

    /**
     * @return int|null
     */
    public function getPaymentCard(): ?int
    {
        return $this->paymentCard;
    }

    /**
     * @param int|null $paymentCard
     * @return Shedule
     */
    public function setPaymentCard(?int $paymentCard): Shedule
    {
        $this->paymentCard = $paymentCard;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getPaymentCash(): ?int
    {
        return $this->paymentCash;
    }

    /**
     * @param int|null $paymentCash
     * @return Shedule
     */
    public function setPaymentCash(?int $paymentCash): Shedule
    {
        $this->paymentCash = $paymentCash;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getPaymentType(): ?int
    {
        return $this->paymentType;
    }

    /**
     * @param int|null $paymentType
     * @return Shedule
     */
    public function setPaymentType(?int $paymentType): Shedule
    {
        $this->paymentType = $paymentType;

        return $this;
    }
}
