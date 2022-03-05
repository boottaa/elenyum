<?php

namespace App\Service;

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
     * @throws \Doctrine\DBAL\Exception
     */
    public function postCollection(array $data): bool
    {
        $start = $data['range']['start'];
        $end = $data['range']['end'];
        //Удаляем за текущей месяц и сохраняем новую колеккцию
        $this->em->getConnection()->executeQuery("DELETE FROM work_schedule WHERE employee_id={$data['employeeId']} AND start>='{$start}' AND end<='{$end}'");

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