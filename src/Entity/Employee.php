<?php

namespace App\Entity;

use App\Repository\EmployeeRepository;
use DateTimeImmutable;
use JsonSerializable;
use \Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherAwareInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=EmployeeRepository::class)
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */
class Employee implements UserInterface, PasswordHasherAwareInterface, PasswordAuthenticatedUserInterface, JsonSerializable
{
    public const STATUS_NEW = 0;
    public const STATUS_CONFIRMED = 1;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $img;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private string $email;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private string $phone;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private ?string $additionalPhone;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private ?\DateTimeImmutable $dateBrith;

    /**
     * У каждого сотрудника свой пароль и свой уровень доступа (доступ по ролям, роли в отдельной таблице)
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $password;

    /**
     * @ORM\Column(type="integer")
     */
    private int $status;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private DateTimeImmutable $updatedAt;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private DateTimeImmutable $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=Company::class, inversedBy="employees")
     * @ORM\JoinColumn(nullable=false)
     */
    private Company $company;

    /**
     * @ORM\ManyToOne(targetEntity=Branch::class, inversedBy="employees")
     * @ORM\JoinColumn(nullable=false)
     */
    private Branch $branch;

    /**
     * @ORM\ManyToOne(targetEntity=Position::class, inversedBy="employee")
     * @ORM\JoinColumn(nullable=false)
     */
    private Position $position;

    public function __construct()
    {
        $this->setCreatedAt(new DateTimeImmutable());
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getAdditionalPhone(): ?string
    {
        return $this->additionalPhone;
    }

    public function setAdditionalPhone(?string $additionalPhone): self
    {
        $this->additionalPhone = $additionalPhone;

        return $this;
    }

    public function getDateBrith(): ?DateTimeImmutable
    {
        return $this->dateBrith;
    }

    public function setDateBrith(?DateTimeImmutable $dateBrith): self
    {
        $this->dateBrith = $dateBrith;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;

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
     * @return $this
     */
    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * @param mixed $updatedAt
     * @return Employee
     */
    public function setUpdatedAt(DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @param DateTimeImmutable $createdAt
     * @return $this
     */
    public function setCreatedAt(DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCompany(): Company
    {
        return $this->company;
    }

    public function setCompany(Company $company): self
    {
        $this->company = $company;

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
     * @return Employee
     */
    public function setBranch(Branch $branch): Employee
    {
        $this->branch = $branch;

        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->getPosition()->getPositionRole()->getRoles();

        return array_map(static function ($item) {
            // For is granted
            return 'ROLE_'.$item;
        }, $roles);
    }

    public function getSalt(): string
    {
        return 'ele';
    }

    public function eraseCredentials(): void
    {
        return;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->getName();
    }

    public function getPasswordHasherName(): ?string
    {
        return PasswordAuthenticatedUserInterface::class;
    }

    public function getUserIdentifier(): ?string
    {
        return base64_encode(md5($this->getName().$this->getId().time()));
    }

    public function getPosition(): ?Position
    {
        return $this->position;
    }

    public function setPosition(?Position $position): self
    {
        $this->position = $position;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getImg(): ?string
    {
        return $this->img;
    }

    /**
     * @param string|null $img
     * @return Employee
     */
    public function setImg(?string $img): Employee
    {
        $this->img = $img;

        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'img' => $this->getImg(),
            'name' => $this->getName(),
            'position' => [
                'id' => $this->getPosition()->getId(),
                'title' => $this->getPosition()->getTitle(),
            ],
            'email' => $this->getEmail(),
            'phone' => $this->getPhone(),
            'additionalPhone' => $this->getAdditionalPhone(),
            'dateBrith' => $this->getDateBrith()?->format('d.m.Y') ?? '-',
        ];
    }
}
