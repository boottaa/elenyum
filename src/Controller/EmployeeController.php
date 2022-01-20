<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Entity\Role;
use App\Exception\ArrayException;
use App\Repository\EmployeeRepository;
use App\Service\EmployeeService;
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
            return $this->json((new ArrayException('Пользователь не найден', 202))->toArray());
        }
        $list = $employeeRepository->findBy(['company' => $user->getCompany()]);

        return $this->json([
            'success' => true,
            'items' => $list,
            'total' => count($list),
        ]);
    }

    #[IsGranted('ROLE_'.Role::EMPLOYEE_GET)]
    #[Route('/api/employee/get/{id<\d+>}', name: 'apiEmployeeGet', methods: 'GET')]
    public function getEmployee(Employee $employee): Response
    {
        $user = $this->getUser();
        if (!$user instanceof Employee) {
            return $this->json((new ArrayException('Пользователь не найден', 202))->toArray());
        }

        return $this->json([
            'success' => true,
            'item' => $employee,
        ]);
    }

    /**
     * @param int $employeeId
     * @param EmployeeService $service
     * @return Response
     * @throws ArrayException
     */
    #[IsGranted('ROLE_'.Role::EMPLOYEE_DELETE)]
    #[Route('/api/employee/delete/{employeeId<\d+>}', name: 'apiEmployeeDelete', methods: 'DELETE')]
    public function delete(int $employeeId, EmployeeService $service): Response
    {
        $user = $this->getUser();
        if (!$user instanceof Employee) {
            return $this->json((new ArrayException('Пользователь не найден', 202))->toArray());
        }
        if ($user->getId() === $employeeId) {
            return $this->json((new ArrayException('Вы не можете удалить сами себя', 202))->toArray());
        }

        $service->del($employeeId);

        return $this->json([
            'success' => true,
        ]);
    }

    /**
     * @param Employee|null $employee
     * @param Request $request
     * @param EmployeeService $service
     * @return Response
     * @throws ArrayException
     * @throws \JsonException
     */
    #[IsGranted('ROLE_'.Role::EMPLOYEE_POST)]
    #[Route('/api/employee/post/{id<\d+>?}', name: 'apiEmployeePost', methods: 'POST')]
    public function post(?Employee $employee, Request $request, EmployeeService $service): Response
    {
        $user = $this->getUser();
        if (!$user instanceof Employee) {
            return $this->json((new ArrayException('Пользователь не найден', 202))->toArray());
        }
        $data['employee'] = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        if($employee === null) {
            $data['user'] = $user;
            $service->add($data);
        } else {
            $service->edit($data);
        }

        return $this->json([
            'success' => true,
        ]);
    }
}
