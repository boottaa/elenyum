<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Entity\Role;
use App\Exception\ArrayException;
use App\Service\ClientService;
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
    #[Route('/api/client/query', name: 'client')]
    public function query(ClientService $service, Request $request): Response
    {
        if (! $this->isGranted(Role::ROLE_SHEDULE_ALL) && ! $this->isGranted(Role::ROLE_SHEDULE_ME)) {
            return $this->json((new ArrayException('Нет прав', 202))->toArray());
        }

        $user = $this->getUser();
        if (!$user instanceof Employee) {
            return $this->json((new ArrayException('Пользователь не найден', 202))->toArray());
        }

        $page = $request->get('page', 1);
        $query = $request->query->getDigits('query', '');
        $list = $service->list(['company' => $user->getCompany(), 'query' => $query], $page);

        return $this->json([
            'success' => true,
            'items' => $list->getResults(),
            'total' => $list->getNumResults(),
            'page' => $list->getCurrentPage(),
            'size' => $list->getPageSize(),
        ]);
    }
}
