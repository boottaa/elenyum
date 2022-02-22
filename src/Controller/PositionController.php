<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Entity\Position;
use App\Entity\PositionRole;
use App\Entity\Role;
use App\Exception\ArrayException;
use App\Repository\PositionRepository;
use App\Service\PositionService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PositionController extends AbstractController
{
    /**
     * @throws ArrayException
     */
    #[IsGranted('ROLE_'.Role::EMPLOYEE_POST)]
    #[Route('/api/position/list', name: 'apiPositionList')]
    public function list(PositionService $service, Request $request): Response
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
     * @param int $positionId
     * @param PositionService $service
     * @return Response
     */
    #[IsGranted('ROLE_'.Role::EMPLOYEE_DELETE)]
    #[Route('/api/position/delete/{positionId<\d+>}', name: 'apiPositionDelete', methods: 'DELETE')]
    public function delete(int $positionId, PositionService $service): Response
    {
        $user = $this->getUser();
        if (!$user instanceof Employee) {
            return $this->json((new ArrayException('Пользователь не найден', 202))->toArray());
        }

        if ($user->getPosition()->getId() === $positionId) {
            return $this->json((new ArrayException('Вы не можете удалить свою роль', 202))->toArray());
        }

        try {
            $service->del($positionId);
        } catch (ArrayException $arrayException) {
            return $this->json($arrayException);
        }

        return $this->json([
            'success' => true,
        ]);
    }

    #[IsGranted('ROLE_'.Role::EMPLOYEE_POST)]
    #[Route('/api/position/get/{positionId<\d+>}', name: 'apiPositionGet', methods: 'GET')]
    public function getPosition(int $positionId, PositionService $service): Response
    {
        $user = $this->getUser();
        if (!$user instanceof Employee) {
            return $this->json((new ArrayException('Пользователь не найден', 202))->toArray());
        }

        return $this->json([
            'success' => true,
            'item' => $service->get($positionId),
        ]);
    }

    /**
     * @throws ArrayException
     * @throws \JsonException
     */
    #[IsGranted('ROLE_'.Role::EMPLOYEE_POST)]
    #[Route('/api/position/post', name: 'apiPositionPost', methods: 'POST')]
    public function post(Request $request, PositionService $service): Response
    {
        $user = $this->getUser();
        if (!$user instanceof Employee) {
            return $this->json((new ArrayException('Пользователь не найден', 202))->toArray());
        }

        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $data['position'] = $data;
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
    #[Route('/api/position/put/{positionId<\d+>?}', name: 'apiPositionPut', methods: 'PUT')]
    public function put(Request $request, PositionService $service): Response
    {
        $user = $this->getUser();
        if (!$user instanceof Employee) {
            return $this->json((new ArrayException('Пользователь не найден', 202))->toArray());
        }

        $data['data'] = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $data['user'] = $user;

        $service->put($data);

        return $this->json([
            'success' => true,
        ]);
    }
}
