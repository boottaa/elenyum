<?php

namespace App\Controller;

use App\Entity\Role;
use App\Repository\OperationRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OperationController extends AbstractController
{
    #[IsGranted('ROLE_' . Role::OPERATION_GET)]
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
