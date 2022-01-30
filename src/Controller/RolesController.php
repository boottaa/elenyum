<?php

namespace App\Controller;

use App\Entity\Role;
use App\Service\ClientService;
use App\Service\RoleService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RolesController extends AbstractController
{
    /**
     * @param RoleService $service
     * @param Request $request
     * @return Response
     * @throws \App\Exception\ArrayException
     */
    #[IsGranted('ROLE_' . Role::CLIENT_GET)]
    #[Route('/api/role/list', name: 'apiRoleList')]
    public function list(RoleService $service, Request $request): Response
    {
        $page = $request->get('page', 1);
        $list = $service->list([], $page);

        return $this->json([
            'success' => true,
            'items' => $list->getResults(),
            'total' => $list->getNumResults(),
            'page' => $list->getCurrentPage(),
            'size' => $list->getPageSize(),
        ]);
    }
}
