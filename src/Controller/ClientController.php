<?php

namespace App\Controller;

use App\Entity\Role;
use App\Repository\ClientRepository;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class ClientController extends AbstractController
{
    /**
     * @param Request $request
     * @param ClientRepository $clientRepository
     * @return Response
     * @throws Exception
     */
    #[IsGranted('ROLE_' . Role::CLIENT_GET)]
    #[Route('/api/client/query', name: 'client')]
    public function query(Request $request, ClientRepository $clientRepository): Response
    {
        $page = $request->get('page', 1);
        $query = $request->query->getDigits('query', '');
        $list = $clientRepository->list(['query' => $query], $page);

        return $this->json([
            'success' => true,
            'items' => $list->getResults(),
            'total' => $list->getNumResults(),
            'page' => $list->getCurrentPage(),
            'size' => $list->getPageSize(),
        ]);
    }
}
