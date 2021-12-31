<?php

namespace App\Controller;

use App\Repository\OperationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OperationController extends AbstractController
{
    #[Route('/operation/list', name: 'operation')]
    public function list(OperationRepository $operationRepository): Response
    {
        $operations = $operationRepository->findAll();
        $total = count($operations);
        return $this->json([
            'total' => $total,
            'items' => $operations,
        ]);
    }
}
