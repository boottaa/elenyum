<?php

namespace App\Controller;

use App\Entity\Role;
use App\Service\ClientService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class ClientController extends AbstractController
{
    /**
     * @param ClientService $service
     * @param Request $request
     * @return Response
     * @throws \App\Exception\ArrayException
     */
    #[IsGranted('ROLE_' . Role::CLIENT_GET)]
    #[Route('/api/client/query', name: 'client')]
    public function query(ClientService $service, Request $request): Response
    {
        $page = $request->get('page', 1);
        $query = $request->query->getDigits('query', '');
        $list = $service->list(['query' => $query], $page);

        return $this->json([
            'success' => true,
            'items' => $list->getResults(),
            'total' => $list->getNumResults(),
            'page' => $list->getCurrentPage(),
            'size' => $list->getPageSize(),
        ]);
    }
}
