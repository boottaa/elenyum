<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Employee;
use App\Entity\Role;
use App\Exception\ArrayException;
use App\Service\ClientService;
use App\Service\EmployeeService;
use App\Validator\EmployeeValidator;
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
    #[Route('/api/client/query', name: 'apiClientQuery')]
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

    /**
     * @param ClientService $service
     * @param Request $request
     * @return Response
     * @throws ArrayException
     */
    #[IsGranted(Role::ROLE_SHEDULE_ALL)]
    #[Route('/api/client/list', name: 'apiClientList')]
    public function list(ClientService $service, Request $request): Response
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

    #[IsGranted(Role::ROLE_SHEDULE_ALL)]
    #[Route('/api/client/get/{clientId<\d+>}', name: 'apiClientGet', methods: 'GET')]
    public function getClient(int $clientId, EmployeeService $service): Response
    {
        $user = $this->getUser();
        if (!$user instanceof Employee) {
            return $this->json((new ArrayException('Пользователь не найден', 202))->toArray());
        }

        return $this->json([
            'success' => true,
            'item' => $service->get($clientId),
        ]);
    }

    /**
     * @param int $clientId
     * @param ClientService $service
     * @return Response
     */
    #[IsGranted(Role::ROLE_SHEDULE_ALL)]
    #[Route('/api/client/delete/{clientId<\d+>}', name: 'apiClientDelete', methods: 'DELETE')]
    public function delete(int $clientId, ClientService $service): Response
    {
        $user = $this->getUser();
        if (!$user instanceof Client) {
            return $this->json((new ArrayException('Пользователь не найден', 202))->toArray());
        }

        try {
            $service->del($clientId);
        } catch (ArrayException $arrayException) {
            return $this->json($arrayException);
        }

        return $this->json([
            'success' => true,
        ]);
    }

    /**
     * Create resource
     *
     * @param Request $request
     * @param ClientService $service
     * @param EmployeeValidator $validator
     * @return Response
     * @throws ArrayException
     * @throws \JsonException
     */
    #[IsGranted(Role::ROLE_SHEDULE_ALL)]
    #[Route('/api/client/post', name: 'apiClientPost', methods: 'POST')]
    public function post(Request $request, ClientService $service, EmployeeValidator $validator): Response
    {
        $content = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        if ($validator->isValid($content)) {
            $user = $this->getUser();
            if (!$user instanceof Client) {
                return $this->json((new ArrayException('Пользователь не найден', 202))->toArray());
            }
            $data['client'] = $content;
            $data['user'] = $user;

            $client = $service->post($data);

            return $this->json([
                'success' => true,
                'item' => $client
            ]);
        }

        return $this->json([
            'success' => false,
            'message' => 'Не корректные данные',
        ]);
    }

    /**
     * Update resource
     *
     * @param Request $request
     * @param EmployeeService $service
     * @param EmployeeValidator $validator
     * @return Response
     * @throws ArrayException
     * @throws \JsonException
     */
    #[IsGranted(Role::ROLE_SHEDULE_ALL)]
    #[Route('/api/client/put/{clientId<\d+>?}', name: 'apiEmployeePut', methods: 'PUT')]
    public function put(Request $request, EmployeeService $service, EmployeeValidator $validator): Response
    {
        $content = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        if ($validator->isValid($content)) {
            $user = $this->getUser();
            if (!$user instanceof Employee) {
                return $this->json((new ArrayException('Пользователь не найден', 202))->toArray());
            }
            $data['client'] = $content;

            $client = $service->put($data);

            return $this->json([
                'success' => true,
                'item' => $client,
            ]);
        }

        return $this->json([
            'success' => false,
            'message' => 'Не корректные данные',
            'errors' => $validator->getErrors()
        ]);
    }
}
