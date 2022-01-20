<?php

namespace App\Service;

use App\Entity\Employee;
use App\Entity\Position;
use App\Exception\ArrayException;
use App\Repository\EmployeeRepository;
use DateTimeImmutable;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;

class EmployeeService extends BaseAbstractService
{
    public function __construct(
        EmployeeRepository $repository,
        private EntityManagerInterface $em
    ) {
        $this->repository = $repository;
    }

    /**
     * @param Employee $employee
     * @param array $data
     * @return void
     * @throws ArrayException
     */
    private function hydrate(Employee $employee, array $data): void
    {
        $position = $this->em->getRepository(Position::class)->find($data['position']['id']);

        if (!$position instanceof Position) {
            throw new ArrayException('Not defined'.Position::class.'by id '.$data['position']['id'], '422');
        }

        $employee->setPosition($position);
        $employee->setName($data['name']);
        $employee->setPhone($data['phone']);
        $employee->setEmail($data['email']);

        $dateBrith = DateTimeImmutable::createFromFormat('U', strtotime($data['dateBrith']))->setTimezone(
            new DateTimeZone('Europe/Moscow')
        );
        $employee->setAdditionalPhone($data['additionalPhone'] ?? null);
        $employee->setPassword($data['password'] ?? null);
        $employee->setDateBrith($dateBrith ?? null);
    }

    /**
     * @param array $data
     * @return bool
     * @throws ArrayException
     */
    public function edit(
        array $data
    ): bool {
        $employeeData = $data['employee'];

        $employee = $this->em->getRepository(Employee::class)->find($employeeData['id']);
        if (!$employee instanceof Employee) {
            throw new ArrayException('Not defined'.Employee::class, '422');
        }
        $this->hydrate($employee, $employeeData);
        $this->em->flush();

        return true;
    }

    /**
     * @param array $data
     * @return bool
     * @throws ArrayException
     */
    public function add(array $data): bool
    {
        $user = $data['user'];
        $employeeData = $data['employee'];

        if (!$user instanceof Employee) {
            throw new ArrayException('Not defined'.Employee::class, '422');
        }

        $employee = new Employee();
        $employee->setCompany($user->getCompany());
        $employee->setBranch($user->getBranch());
        $employee->setStatus(Employee::STATUS_CONFIRMED);
        $employee->setApiToken(null);
        $this->em->persist($employee);

        $this->hydrate($employee, $employeeData);
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
        $employee = $this->em->find(Employee::class, $id);
        if (!$employee instanceof Employee) {
            throw new ArrayException('Not defined'.Employee::class, '422');
        }

        $this->em->remove($employee);
        $this->em->flush();

        return true;
    }
}