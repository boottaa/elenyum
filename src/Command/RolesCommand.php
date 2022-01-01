<?php

namespace App\Command;

use App\Entity\Role;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:roles',
    description: 'Add a short description for your command',
)]
class RolesCommand extends Command
{
    public function __construct(private EntityManagerInterface $em)
    {
        parent::__construct('app:roles');
    }

    protected function configure(): void
    {
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $roles = [
            Role::SHEDULE_POST => ['title' => 'SHEDULE_POST', 'description' => 'Добавление записей к специалисту'],
            Role::SHEDULE_DELETE => ['title' => 'SHEDULE_DELETE', 'description' => 'Удаление записей к специалисту'],
            Role::SHEDULE_GET => ['title' => 'SHEDULE_GET', 'description' => 'Получения записей к специалисту'],
            Role::OPERATION_POST => ['title' => 'OPERATION_POST', 'description' => 'Добавление операций'],
            Role::OPERATION_DELETE => ['title' => 'OPERATION_DELETE', 'description' => 'Удаление операций'],
            Role::OPERATION_GET => ['title' => 'OPERATION_GET', 'description' => 'Получения операций'],
            Role::CLIENT_POST => ['title' => 'CLIENT_POST', 'description' => 'Добавление клиентов'],
            Role::CLIENT_DELETE => ['title' => 'CLIENT_DELETE', 'description' => 'Удаление клиентов'],
            Role::CLIENT_GET => ['title' => 'CLIENT_GET', 'description' => 'Получения клиентов'],
            Role::EMPLOYEE_POST => ['title' => 'EMPLOYEE_POST', 'description' => 'Добавление и редактирование данных сотрудников'],
            Role::EMPLOYEE_DELETE => ['title' => 'EMPLOYEE_DELETE', 'description' => 'Удаление сотрудников'],
            Role::EMPLOYEE_GET => ['title' => 'EMPLOYEE_GET', 'description' => 'Получения сотрудников'],
            Role::EMPLOYEE_ROLE_POST => ['title' => 'EMPLOYEE_ROLE_POST', 'description' => 'Добавление и редактирование ролей пользователей'],
            Role::EMPLOYEE_ROLE_DELETE => ['title' => 'EMPLOYEE_ROLE_DELETE', 'description' => 'Удаление ролей пользователей'],
            Role::EMPLOYEE_ROLE_GET => ['title' => 'EMPLOYEE_ROLE_GET', 'description' => 'Получения ролей пользователей'],
            Role::BRANCH_POST => ['title' => 'BRANCH_POST', 'description' => 'Добавление и редактирование филиалов'],
            Role::BRANCH_DELETE => ['title' => 'BRANCH_DELETE', 'description' => 'Удаление филиалов'],
            Role::BRANCH_GET => ['title' => 'BRANCH_GET', 'description' => 'Получение филиалов'],
            Role::COMMODITY_POST => ['title' => 'COMMODITY_POST', 'description' => 'Добавление и редактирование товаров'],
            Role::COMMODITY_DELETE => ['title' => 'COMMODITY_DELETE', 'description' => 'Удаление товаров'],
            Role::COMMODITY_GET => ['title' => 'COMMODITY_GET', 'description' => 'Получение товаров'],
            Role::TECHNOLOGICAL_MAP_POST => ['title' => 'TECHNOLOGICAL_MAP_POST', 'description' => 'Добавление и редактирование технологической карты'],
            Role::TECHNOLOGICAL_MAP_DELETE => ['title' => 'TECHNOLOGICAL_MAP_DELETE', 'description' => 'Удаление технологической карты'],
            Role::TECHNOLOGICAL_MAP_GET => ['title' => 'TECHNOLOGICAL_MAP_GET', 'description' => 'Получения технологических карт'],
        ];

        $this->em->getConnection()->prepare('DELETE FROM role;')->executeQuery();
        foreach ($roles as $key => $role) {
            $roleEntity = new Role();
            $roleEntity->setId($key);
            $roleEntity->setTitle($role['title']);
            $roleEntity->setDescription($role['description']);

            $this->em->persist($roleEntity);
        }

        $this->em->flush();

        $io->success('Roles added!');

        return Command::SUCCESS;
    }
}
