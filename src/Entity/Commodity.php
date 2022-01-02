<?php

namespace App\Entity;

use App\Repository\CommodityRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CommodityRepository::class)
 */
class Commodity
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
    private string $title;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $code;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $barcode;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $category;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $unit;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $netWeight;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $grossWeight;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $sellingPrice;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $costPrice;

    /**
     * Система налогообложения
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $taxSystem;

    /**
     * Налог в %
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $vat;

    /**
     * Количество
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $quantities;

    /**
     * Минимальное количество товара
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $criticalRemainder;

    /**
     * Желаемый остаток
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $desiredBalance;

    /**
     * @ORM\ManyToOne(targetEntity=Branch::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private Branch $branch;

    public function getId(): int
    {
        return $this->id;
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

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getBarcode(): ?string
    {
        return $this->barcode;
    }

    public function setBarcode(?string $barcode): self
    {
        $this->barcode = $barcode;

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(?string $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getUnit(): ?string
    {
        return $this->unit;
    }

    public function setUnit(?string $unit): self
    {
        $this->unit = $unit;

        return $this;
    }

    public function getNetWeight(): ?string
    {
        return $this->netWeight;
    }

    public function setNetWeight(?string $netWeight): self
    {
        $this->netWeight = $netWeight;

        return $this;
    }

    public function getGrossWeight(): ?string
    {
        return $this->grossWeight;
    }

    public function setGrossWeight(?string $grossWeight): self
    {
        $this->grossWeight = $grossWeight;

        return $this;
    }

    public function getSellingPrice(): ?int
    {
        return $this->sellingPrice;
    }

    public function setSellingPrice(?int $sellingPrice): self
    {
        $this->sellingPrice = $sellingPrice;

        return $this;
    }

    public function getCostPrice(): ?int
    {
        return $this->costPrice;
    }

    public function setCostPrice(?int $costPrice): self
    {
        $this->costPrice = $costPrice;

        return $this;
    }

    public function getTaxSystem(): ?string
    {
        return $this->taxSystem;
    }

    public function setTaxSystem(?string $taxSystem): self
    {
        $this->taxSystem = $taxSystem;

        return $this;
    }

    public function getVat(): ?int
    {
        return $this->vat;
    }

    public function setVat(?int $vat): self
    {
        $this->vat = $vat;

        return $this;
    }

    public function getQuantities(): ?int
    {
        return $this->quantities;
    }

    public function setQuantities(?int $quantities): self
    {
        $this->quantities = $quantities;

        return $this;
    }

    public function getCriticalRemainder(): ?int
    {
        return $this->criticalRemainder;
    }

    public function setCriticalRemainder(?int $criticalRemainder): self
    {
        $this->criticalRemainder = $criticalRemainder;

        return $this;
    }

    public function getDesiredBalance(): ?int
    {
        return $this->desiredBalance;
    }

    public function setDesiredBalance(?int $desiredBalance): self
    {
        $this->desiredBalance = $desiredBalance;

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
     */
    public function setBranch(Branch $branch): void
    {
        $this->branch = $branch;
    }
}
