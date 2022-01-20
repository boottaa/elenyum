<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Entity\Role;
use App\Exception\ArrayException;
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
        $user = $this->getUser();
        if (!$user instanceof Employee) {
            return $this->json((new ArrayException('Пользователь не найден', 202))->toArray());
        }

        $operations = $operationRepository->findBy(['company' => $user->getCompany()]);
        $total = count($operations);
        return $this->json([
            'total' => $total,
            'items' => $operations,
        ]);
    }
}
