<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Entity\Operation;
use App\Entity\Role;
use App\Exception\ArrayException;
use App\Service\OperationService;
use App\Validator\OperationValidator;
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
    #[IsGranted(Role::ROLE_OPERATION_EDIT)]
    #[Route('/api/operation/list', name: 'operation')]
    public function list(OperationService $service, Request $request): Response
    {
        $user = $this->getUser();
        if (!$user instanceof Employee) {
            return $this->json(new ArrayException('Пользователь не найден', 202));
        }

        $list = $service->list([
            'company' => $user->getCompany(),
            'employee' => $request->get('employee'),
        ], $request->get('page', 1));

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
    #[IsGranted(Role::ROLE_OPERATION_EDIT)]
    #[Route('/api/operation/delete/{operationId<\d+>}', name: 'apiOperationDelete', methods: 'DELETE')]
    public function delete(int $operationId, OperationService $service): Response
    {
        $user = $this->getUser();
        if (!$user instanceof Employee) {
            return $this->json(new ArrayException('Пользователь не найден', 202));
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

    #[IsGranted(Role::ROLE_OPERATION_EDIT)]
    #[Route('/api/operation/get/{id<\d+>}', name: 'apiOperationGet', methods: 'GET')]
    public function getOperation(Operation $operation): Response
    {
        $user = $this->getUser();
        if (!$user instanceof Employee) {
            return $this->json(new ArrayException('Пользователь не найден', 202));
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
    #[IsGranted(Role::ROLE_OPERATION_EDIT)]
    #[Route('/api/operation/post', name: 'apiOperationPost', methods: 'POST')]
    public function post(Request $request, OperationService $service, OperationValidator $validator): Response
    {
        $content = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        if ($validator->isValid($content)) {
            $user = $this->getUser();
            if (!$user instanceof Employee) {
                return $this->json(new ArrayException('Пользователь не найден', 202));
            }

            $data['operation'] = $content;
            $data['user'] = $user;

            $operation = $service->post($data);

            return $this->json([
                'success' => true,
                'item' => $operation
            ]);
        }

        return $this->json([
            'success' => false,
            'message' => 'Не корректные данные',
            'errors' => $validator->getErrors(),
        ]);
    }

    /**
     * @throws ArrayException
     * @throws \JsonException
     */
    #[IsGranted(Role::ROLE_OPERATION_EDIT)]
    #[Route('/api/operation/put/{operationId<\d+>?}', name: 'apiOperationPut', methods: 'PUT')]
    public function put(Request $request, OperationService $service, OperationValidator $validator): Response
    {
        $content = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        if ($validator->isValid($content)) {
            $user = $this->getUser();
            if (!$user instanceof Employee) {
                return $this->json(new ArrayException('Пользователь не найден', 202));
            }

            $data['operation'] = $content;
            $data['user'] = $user;

            $operation = $service->put($data);

            return $this->json([
                'success' => true,
                'item' => $operation,
            ]);
        }

        return $this->json([
            'success' => false,
            'message' => 'Не корректные данные',
            'errors' => $validator->getErrors(),
        ]);
    }
}
