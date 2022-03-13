<?php

namespace App\Service;

use App\Entity\Employee;
use App\Entity\Operation;
use App\Entity\Position;
use App\Entity\PositionOperation;
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

    public function get(int $id): array
    {
        $item = $this->repository->get($id);

        $result = [];
        if (!empty($item)) {
            $result = [
                'id' => $item['id'],
                'title' => $item['title'],
                'inCalendar' => $item['inCalendar'],
                'roles' => array_map(
                    static function ($item) {
                        return (int) $item;
                    },
                    explode('.', $item['positionRole']['roles'] ?? [])
                ),
                'operations' => array_map(static function ($item) {
                    return $item['operation']['id'];
                }, $item['positionOperation'] ?? []),
            ];
        }

        return $result;
    }

    /**
     * @param Position $position
     * @param array $data
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    private function hydrate(Position $position, array $data): void
    {
        $position->setInCalendar((bool)$data['inCalendar']);
        $position->setTitle($data['title']);
        $this->em->persist($position);
        $positionRole = new PositionRole();
        $positionRole->setPosition($position);

        if ($id = $position->getId()) {
            $this->em->getConnection()->executeQuery("DELETE FROM position_role WHERE position_id={$id}");
        }
        foreach ($data['roles'] as $role) {
            $positionRole->addRole($role['id']);
        }

        if ($id = $position->getId()) {
            $this->em->getConnection()->executeQuery("DELETE FROM position_operation WHERE position_id={$id}");
        }
        $operations = $this->em->getRepository(Operation::class)->findBy(['id' => $data['operations']]);

        foreach ($operations as $operation) {
            if ($operation instanceof Operation) {
                $positionOperation = new PositionOperation();
                $positionOperation->setPosition($position);
                $positionOperation->setOperation($operation);
                $this->em->persist($positionOperation);
            }
        }

        $this->em->persist($positionRole);
    }

    /**
     * @param array $data
     * @return Position
     * @throws ArrayException
     * @throws \Doctrine\DBAL\Exception
     */
    public function put(array $data): Position
    {
        $positionData = $data['data'];
        $position = $this->em->getRepository(Position::class)->find($positionData['id']);
        if (!$position instanceof Position) {
            throw new ArrayException('Not defined'.Position::class, '422');
        }
        $this->hydrate($position, $positionData);
        $this->em->flush();

        return $position;
    }

    /**
     * @param array $data
     * @return Position
     * @throws ArrayException
     * @throws \Doctrine\DBAL\Exception
     */
    public function post(array $data): Position
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

        return $position;
    }

    /**
     * @param int $id
     * @return bool
     * @throws ArrayException
     */
    public function del(int $id): bool
    {
        $employees = $this->em->getRepository(Employee::class)->findBy(['position' => $id]);
        if (count($employees) !== 0) {
            throw new ArrayException('Невозможно удалить пока есть пользователь с такой должностью', 202);
        }
        $position = $this->em->find(Position::class, $id);
        if (!$position instanceof Position) {
            throw new ArrayException('Not defined '.Position::class, '422');
        }

        $this->em->getConnection()->executeQuery("DELETE FROM position_operation WHERE position_id={$id}");
        $this->em->remove($position->getPositionRole());
        $this->em->remove($position);
        $this->em->flush();

        return true;
    }
}