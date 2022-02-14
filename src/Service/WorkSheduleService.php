<?php

namespace App\Service;

use App\Entity\Branch;
use App\Entity\Employee;
use App\Entity\WorkSchedule;
use App\Repository\WorkScheduleRepository;
use DateTimeImmutable;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;

class WorkSheduleService extends BaseAbstractService
{
    public function __construct(
        WorkScheduleRepository $repository,
        private EntityManagerInterface $em
    ) {
        $this->repository = $repository;
    }

    /**
     * @param WorkSchedule $workSchedule
     * @param Employee $employee
     * @param array $data
     * @return void
     */
    private function hydrate(WorkSchedule $workSchedule, Employee $employee, array $data): void
    {
        $workSchedule->setEmployee($employee);

        $start = DateTimeImmutable::createFromFormat('U', strtotime($data['start']))->setTimezone(
            new DateTimeZone('Europe/Moscow')
        );
        $end = DateTimeImmutable::createFromFormat('U', strtotime($data['end']))->setTimezone(
            new DateTimeZone('Europe/Moscow')
        );
        $workSchedule->setStart($start);
        $workSchedule->setEnd($end);
    }

    /**
     * @param array $data
     * @return bool
     */
    public function postCollection(array $data): bool
    {
        $employee = $this->em->getRepository(Employee::class)->find($data['employeeId']);
        $items = $data['data'];

        foreach ($items as $item) {
            $workSchedule = new WorkSchedule();
            $this->hydrate($workSchedule, $employee, $item);
            $this->em->persist($workSchedule);
        }
        $this->em->flush();

        return true;
    }
}