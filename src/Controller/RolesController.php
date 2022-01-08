<?php

namespace App\Controller;

use App\Entity\Role;
use App\Repository\RoleRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RolesController extends AbstractController
{
    #[IsGranted('ROLE_' . Role::EMPLOYEE_POST)]
    #[Route('/api/role/list', name: 'roleList')]
    public function index(RoleRepository $roleRepository): Response
    {
        $roles = $roleRepository->findAll();
        $total = count($roles);
        return $this->json([
            'total' => $total,
            'items' => $roles,
        ]);
    }
}
