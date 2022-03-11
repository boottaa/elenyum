<?php

namespace App\Service;

use App\Entity\Employee;
use App\Entity\Position;
use App\Exception\ArrayException;
use App\Repository\EmployeeRepository;
use App\Repository\ListRepositoryInterface;
use App\Utils\Paginator;
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
     * @throws ArrayException
     */
    public function listForCalendar(?array $params): Paginator
    {
        return $this->repository->listForCalendar($params);
    }


    public function get(int $id): array
    {
        return $this->repository->get($id);
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

        if (!empty($data['dateBrith'])) {
            $dateBrith = DateTimeImmutable::createFromFormat('U', strtotime($data['dateBrith']))->setTimezone(
                new DateTimeZone('Europe/Moscow')
            );
            $employee->setDateBrith($dateBrith ?? null);
        }
        $employee->setAdditionalPhone($data['additionalPhone'] ?? null);
        $password = $data['password'] ?? null;
        if (!empty($password)) {
            $employee->setPassword($password);
        }
    }

    /**
     * @param array $data
     * @return Employee
     * @throws ArrayException
     */
    public function put(array $data): Employee
    {
        $employeeData = $data['employee'];

        $employee = $this->em->getRepository(Employee::class)->find($employeeData['id']);
        if (!$employee instanceof Employee) {
            throw new ArrayException('Not defined'.Employee::class, '422');
        }
        $this->hydrate($employee, $employeeData);
        $this->em->flush();

        return $employee;
    }

    /**
     * @param array $data
     * @return Employee
     * @throws ArrayException
     */
    public function post(array $data): Employee
    {
        $user = $data['user'];
        $employeeData = $data['employee'];

        if (!$user instanceof Employee) {
            throw new ArrayException('Not defined '.Employee::class, '422');
        }

        $employee = new Employee();
        $employee->setCompany($user->getCompany());
        $employee->setBranch($user->getBranch());
        $employee->setStatus(Employee::STATUS_CONFIRMED);
        $this->em->persist($employee);

        $this->hydrate($employee, $employeeData);
        $this->em->flush();

        return $employee;
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