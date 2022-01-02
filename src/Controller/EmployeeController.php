<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Entity\Role;
use App\Exception\ArrayException;
use App\Repository\EmployeeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EmployeeController extends AbstractController
{
    #[IsGranted('ROLE_' . Role::EMPLOYEE_GET)]
    #[Route('/api/employee/list', name: 'employeeList')]
    public function list(EmployeeRepository $employeeRepository): Response
    {
        $user = $this->getUser();
        if (!$user instanceof Employee) {
            return $this->json((new ArrayException('User undefined', 202))->toArray());
        }
        $list = $employeeRepository->getList($user->getCompany());

        return $this->json([
            'items' => $list,
            'total' => count($list)
        ]);
    }

    /**
     * @throws \JsonException
     * @throws ArrayException
     */
    #[IsGranted('ROLE_' . Role::EMPLOYEE_POST)]
    #[Route('/api/employee/post', name: 'employeePost', methods: 'POST')]
    public function post(Request $request, EmployeeRepository $employeeRepository, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user instanceof Employee) {
            return $this->json((new ArrayException('User undefined', 202))->toArray());
        }
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $employee = new Employee();
        $employee->setCompany($user->getCompany());
        $employee->setStatus(Employee::STATUS_CONFIRMED);
        $employee->setName($data['name']);
        $employee->setPhone($data['phone']);
        $employee->setAdditionalPhone($data['additionalPhone']);
        $employee->setEmail($data['email']);

        $em->persist($employee);
        $em->flush();

        return $this->json([]);
    }
}
