<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Repository\EmployeeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EmployeeController extends AbstractController
{
    #[Route('/api/employee/list', name: 'employeeList')]
    public function list(EmployeeRepository $employeeRepository): Response
    {
        $list = $employeeRepository->getList();

        return $this->json([
            'items' => $list,
            'total' => count($list)
        ]);
    }

    /**
     * @throws \JsonException
     */
    #[Route('/api/employee/post', name: 'employeePost', methods: 'POST')]
    public function post(Request $request, EmployeeRepository $employeeRepository): Response
    {
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $employee = new Employee();
//        $employee->set
//
//        return $this->json([
//            'items' => $list,
//            'total' => count($list)
//        ]);

        return $this->json([]);
    }
}
