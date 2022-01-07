<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Entity\Role;
use App\Exception\ArrayException;
use App\Repository\PositionRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PositionController extends AbstractController
{
    #[IsGranted('ROLE_' . Role::EMPLOYEE_POST)]
    #[Route('/api/position/list', name: 'positionList')]
    public function index(PositionRepository $positionRepository): Response
    {
        $user = $this->getUser();
        if (!$user instanceof Employee) {
            return $this->json((new ArrayException('User undefined', 202))->toArray());
        }

        $positions = $positionRepository->findBy(['company' => $user->getCompany()]);
        $total = count($positions);
        return $this->json([
            'total' => $total,
            'items' => $positions,
        ]);
    }
}
