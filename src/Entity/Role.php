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
    private const BRANCH_SETTING = 1;
    private const SHEDULE_ME = 2;
    private const SHEDULE_ALL = 3;
    private const POSITION_EDIT = 4;
    private const EMPLOYEE_EDIT = 5;
    private const OPERATION_EDIT = 6;
    private const WORK_SCHEDULE_EDIT = 7;
    private const WORK_SCHEDULE_VIEW = 8;

    public const ROLE_BRANCH_SETTING = 'ROLE_BRANCH_SETTING';
    public const ROLE_SHEDULE_ME = 'ROLE_SHEDULE_ME';
    public const ROLE_SHEDULE_ALL = 'ROLE_SHEDULE_ALL';
    public const ROLE_POSITION_EDIT = 'ROLE_POSITION_EDIT';
    public const ROLE_EMPLOYEE_EDIT = 'ROLE_EMPLOYEE_EDIT';
    public const ROLE_OPERATION_EDIT = 'ROLE_OPERATION_EDIT';
    public const ROLE_WORK_SCHEDULE_EDIT = 'ROLE_WORK_SCHEDULE_EDIT';
    public const ROLE_WORK_SCHEDULE_VIEW = 'ROLE_WORK_SCHEDULE_VIEW';

    public const ROLES = [
        Role::BRANCH_SETTING => [
            'title' => self::ROLE_BRANCH_SETTING,
            'description' => 'Настройка филиала'
        ],
        Role::SHEDULE_ME => [
            'title' => self::ROLE_SHEDULE_ME,
            'description' => 'Получение записей только по себе'
        ],
        Role::SHEDULE_ALL => [
            'title' => self::ROLE_SHEDULE_ALL,
            'description' => 'Получение записей по всем сотрудников и добавление, редактирование и удаление записей',
        ],
        Role::POSITION_EDIT => [
            'title' => self::ROLE_POSITION_EDIT,
            'description' => 'Добавление, редактирование и удаление должностей',
        ],
        Role::EMPLOYEE_EDIT => [
            'title' => self::ROLE_EMPLOYEE_EDIT,
            'description' => 'Добавление, редактирование и удаление сотрудников',
        ],
        Role::OPERATION_EDIT => [
            'title' => self::ROLE_OPERATION_EDIT,
            'description' => 'Добавление, редактирование и удаление услуг',
        ],
        Role::WORK_SCHEDULE_EDIT => [
            'title' => self::ROLE_WORK_SCHEDULE_EDIT,
            'description' => 'Добавление, редактирование и удаление графика работы',
        ],
        Role::WORK_SCHEDULE_VIEW => [
            'title' => self::ROLE_WORK_SCHEDULE_VIEW,
            'description' => 'Просмотр графика работы'
        ],
    ];

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
