<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Entity\Operation;
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

    /**
     * @param int $operationId
     * @param OperationService $service
     * @return Response
     */
    #[IsGranted('ROLE_'.Role::EMPLOYEE_DELETE)]
    #[Route('/api/operation/delete/{operationId<\d+>}', name: 'apiOperationDelete', methods: 'DELETE')]
    public function delete(int $operationId, OperationService $service): Response
    {
        $user = $this->getUser();
        if (!$user instanceof Employee) {
            return $this->json((new ArrayException('Пользователь не найден', 202))->toArray());
        }

        try {
            $service->del($operationId);
        } catch (ArrayException $arrayException) {
            return $this->json($arrayException);
        }

        return $this->json([
            'success' => true,
        ]);
    }

    #[IsGranted('ROLE_'.Role::EMPLOYEE_POST)]
    #[Route('/api/operation/get/{id<\d+>}', name: 'apiOperationGet', methods: 'GET')]
    public function getOperation(Operation $operation): Response
    {
        $user = $this->getUser();
        if (!$user instanceof Employee) {
            return $this->json((new ArrayException('Пользователь не найден', 202))->toArray());
        }

        return $this->json([
            'success' => true,
            'item' => $operation,
        ]);
    }

    /**
     * @throws ArrayException
     * @throws \JsonException
     */
    #[IsGranted('ROLE_'.Role::EMPLOYEE_POST)]
    #[Route('/api/operation/post', name: 'apiOperationPost', methods: 'POST')]
    public function post(Request $request, OperationService $service): Response
    {
        $user = $this->getUser();
        if (!$user instanceof Employee) {
            return $this->json((new ArrayException('Пользователь не найден', 202))->toArray());
        }

        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $data['operation'] = $data;
        $data['user'] = $user;

        $service->post($data);

        return $this->json([
            'success' => true,
        ]);
    }

    /**
     * @throws ArrayException
     * @throws \JsonException
     */
    #[IsGranted('ROLE_'.Role::EMPLOYEE_POST)]
    #[Route('/api/operation/put', name: 'apiOperationPut', methods: 'PUT')]
    public function put(Request $request, OperationService $service): Response
    {
        $user = $this->getUser();
        if (!$user instanceof Employee) {
            return $this->json((new ArrayException('Пользователь не найден', 202))->toArray());
        }

        $data['operation'] = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $data['user'] = $user;

        $service->put($data);

        return $this->json([
            'success' => true,
        ]);
    }
}
