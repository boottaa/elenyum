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

        $roles = Role::ROLES;

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
