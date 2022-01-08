<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Entity\Position;
use App\Entity\Role;
use App\Exception\ArrayException;
use App\Repository\EmployeeRepository;
use App\Repository\PositionRepository;
use DateTimeImmutable;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EmployeeController extends AbstractController
{
    #[IsGranted('ROLE_'.Role::EMPLOYEE_GET)]
    #[Route('/api/employee/list', name: 'apiEmployeeList')]
    public function list(EmployeeRepository $employeeRepository): Response
    {
        $user = $this->getUser();
        if (!$user instanceof Employee) {
            return $this->json((new ArrayException('User undefined', 202))->toArray());
        }
        $list = $employeeRepository->findBy(['company' => $user->getCompany()]);

        return $this->json([
            'success' => true,
            'items' => $list,
            'total' => count($list),
        ]);
    }

    /**
     * @param Request $request
     * @param PositionRepository $positionRepository
     * @param EntityManagerInterface $em
     * @return Response
     * @throws \JsonException
     */
    #[IsGranted('ROLE_'.Role::EMPLOYEE_POST)]
    #[Route('/api/employee/post', name: 'apiEmployeePost', methods: 'POST')]
    public function post(Request $request, PositionRepository $positionRepository, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user instanceof Employee) {
            return $this->json((new ArrayException('User undefined', 202))->toArray());
        }
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $position = $positionRepository->findOneBy(['id' => $data['positionId']]);
        if (!$position instanceof Position) {
            return $this->json(new ArrayException('Позиция с не найдена, попробуйте снова'));
        }

        $employee = new Employee();
        $employee->setCompany($user->getCompany());
        $employee->setBranch($user->getBranch());
        $employee->setPosition($position);
        $employee->setStatus(1);
        $employee->setStatus(Employee::STATUS_CONFIRMED);
        $employee->setName($data['userName']);
        $employee->setPhone($data['phone']);
        $employee->setEmail($data['email']);
        $employee->setApiToken(null);

        $dateBrith = DateTimeImmutable::createFromFormat('U', strtotime($data['dateBrith']))->setTimezone(
            new DateTimeZone('Europe/Moscow')
        );
        $employee->setAdditionalPhone($data['additionalPhone'] ?? null);
        $employee->setPassword($data['password'] ?? null);
        $employee->setDateBrith($dateBrith ?? null);

        $em->persist($employee);
        $em->flush();

        return $this->json([
            'success' => true,
        ]);
    }
}
