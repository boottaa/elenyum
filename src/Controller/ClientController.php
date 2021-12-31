<?php

namespace App\Controller;

use App\Repository\ClientRepository;
use Exception;
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
    #[Route('/client/list', name: 'client')]
    public function list(Request $request, ClientRepository $clientRepository): Response
    {
        $page = $request->query->getInt('page', 1);
        $query = $request->query->getDigits('query', '');
        $pagginator = $clientRepository->getList($page, $query);

        return $this->json([
            'total' => $pagginator->getNumResults(),
            'items' => $pagginator->getResults(),
        ]);
    }
}
