<?php

namespace App\Service;

use App\Entity\Branch;
use App\Exception\ArrayException;
use App\Repository\BranchRepository;
use DateTimeImmutable;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;

class BranchService extends BaseAbstractService
{
    public function __construct(
        BranchRepository $repository,
        private EntityManagerInterface $em
    ) {
        $this->repository = $repository;
    }

    /**
     * @param Branch $branch
     * @param array $data
     * @return void
     */
    private function hydrate(Branch $branch, array $data): void
    {
        $branch->setName($data['name']);

        $start = DateTimeImmutable::createFromFormat('U', strtotime($data['start']))->setTimezone(
            new DateTimeZone('Europe/Moscow')
        );
        $end = DateTimeImmutable::createFromFormat('U', strtotime($data['end']))->setTimezone(
            new DateTimeZone('Europe/Moscow')
        );
        $branch->setStart($start);
        $branch->setEnd($end);
    }

    /**
     * @param array $data
     * @return bool
     */
    public function put(array $data): bool
    {
        $branch = $data['branch'];
        $data = $data['data'];

        $this->hydrate($branch, $data);
        $this->em->flush();

        return true;
    }

}