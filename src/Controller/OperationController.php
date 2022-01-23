<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Entity\Role;
use App\Exception\ArrayException;
use App\Service\OperationService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OperationController extends AbstractController
{
    /**
     * @param OperationService $service
     * @param Request $request
     * @return Response
     * @throws ArrayException
     */
    #[IsGranted('ROLE_' . Role::OPERATION_GET)]
    #[Route('/api/operation/list', name: 'operation')]
    public function list(OperationService $service, Request $request): Response
    {
        $page = $request->get('page', 1);
        $user = $this->getUser();
        if (!$user instanceof Employee) {
            return $this->json((new ArrayException('Пользователь не найден', 202))->toArray());
        }
        $list = $service->list(['company' => $user->getCompany()], $page);

        return $this->json([
            'success' => true,
            'items' => $list->getResults(),
            'total' => $list->getNumResults(),
            'page' => $list->getCurrentPage(),
            'size' => $list->getPageSize(),
        ]);
    }
}
