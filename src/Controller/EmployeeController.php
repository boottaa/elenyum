<?php

namespace App\Controller;

use App\Repository\EmployeeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EmployeeController extends AbstractController
{
    #[Route('/employee/list', name: 'employee')]
    public function index(EmployeeRepository $employeeRepository): Response
    {
        $list = $employeeRepository->getList();

        return $this->json([
            'items' => $list,
            'total' => count($list)
        ]);
    }
}
