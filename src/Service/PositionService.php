<?php

namespace App\Service;

use App\Entity\Employee;
use App\Entity\Position;
use App\Entity\PositionRole;
use App\Exception\ArrayException;
use App\Repository\PositionRepository;
use Doctrine\ORM\EntityManagerInterface;

class PositionService extends BaseAbstractService
{
    public function __construct(
        PositionRepository $repository,
        private EntityManagerInterface $em
    ) {
        $this->repository = $repository;
    }

    /**
     * @param Position $position
     * @param array $data
     * @return void
     */
    private function hydrate(Position $position, array $data): void
    {
        $position->setInCalendar($data['inCalendar']);
        $position->setTitle($data['title']);
        $this->em->persist($position);
        $positionRole = new PositionRole();
        $positionRole->setPosition($position);

        foreach ($data['roles'] as $role) {
            $positionRole->addRole($role['id']);
        }

        $this->em->persist($positionRole);
    }

    /**
     * @param array $data
     * @return bool
     * @throws ArrayException
     */
    public function put(array $data): bool {
        $positionData = $data['position'];
        $position = $this->em->getRepository(Position::class)->find($positionData['id']);
        if (!$position instanceof Position) {
            throw new ArrayException('Not defined'.Position::class, '422');
        }
        $this->hydrate($position, $positionData);
        $this->em->flush();

        return true;
    }

    /**
     * @param array $data
     * @return bool
     * @throws ArrayException
     */
    public function post(array $data): bool
    {
        $user = $data['user'];
        $positionData = $data['position'];

        if (!$user instanceof Employee) {
            throw new ArrayException('Not defined'.Employee::class, '422');
        }

        $position = new Position();
        $position->setCompany($user->getCompany());
        $this->hydrate($position, $positionData);
        $this->em->flush();

        return true;
    }

    /**
     * @param int $id
     * @return bool
     * @throws ArrayException
     */
    public function del(int $id): bool
    {
        $position = $this->em->find(Position::class, $id);
        if (!$position instanceof Position) {
            throw new ArrayException('Not defined'.Position::class, '422');
        }

        $this->em->remove($position->getPositionRole());
        $this->em->remove($position);
        $this->em->flush();

        return true;
    }
}