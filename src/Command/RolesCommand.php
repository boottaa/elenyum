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
            Role::BRANCH_SETTING => ['title' => 'BRANCH_SETTING', 'description' => 'Настройка филиала'],
            Role::SHEDULE_ME => ['title' => 'SHEDULE_ME', 'description' => 'Получение записей только по себе'],
            Role::SHEDULE_ALL => ['title' => 'SHEDULE_ALL', 'description' => 'Получение записей по всем сотрудников и добавление, редактирование и удаление записей'],
            Role::POSITION_EDIT => ['title' => 'POSITION_EDIT', 'description' => 'Добавление, редактирование и удаление должностей'],
            Role::EMPLOYEE_EDIT => ['title' => 'EMPLOYEE_EDIT', 'description' => 'Добавление, редактирование и удаление сотрудников'],
            Role::OPERATION_EDIT => ['title' => 'OPERATION_EDIT', 'description' => 'Добавление, редактирование и удаление услуг'],
            Role::WORK_SCHEDULE_EDIT => ['title' => 'WORK_SCHEDULE_EDIT', 'description' => 'Добавление, редактирование и удаление графика работы'],
            Role::WORK_SCHEDULE_VIEW => ['title' => 'WORK_SCHEDULE_VIEW', 'description' => 'Просмотр графика работы'],
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
