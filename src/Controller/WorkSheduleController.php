<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Entity\Role;
use App\Exception\ArrayException;
use App\Service\BranchService;
use App\Service\WorkSheduleService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WorkSheduleController extends AbstractController
{
    /**
     * @param int $userId
     * @param Request $request
     * @return Response
     */
    #[IsGranted('ROLE_'.Role::EMPLOYEE_POST)]
    #[Route('/api/workShedule/list/{userId<\d+>?}', name: 'apiWorkSheduleListByUser')]
    public function list(int $userId, Request $request): Response
    {
        $user = $this->getUser();
        if (!$user instanceof Employee) {
            return $this->json((new ArrayException('Пользователь не найден', 202))->toArray());
        }

        return $this->json([
            'success' => true,
            'items' => $user,
        ]);
    }

    /**
     * @param Request $request
     * @param WorkSheduleService $service
     * @return Response
     * @throws ArrayException
     * @throws \JsonException
     */
    #[IsGranted('ROLE_'.Role::EMPLOYEE_POST)]
    #[Route('/api/workShedule/post/collection', name: 'apiWorkShedulePostCollectionByUser', methods: 'POST')]
    public function post(Request $request, WorkSheduleService $service): Response
    {
        $user = $this->getUser();
        if (!$user instanceof Employee) {
            return $this->json((new ArrayException('Пользователь не найден', 202))->toArray());
        }

        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $result = [
            'employeeId' => $data['employeeId'],
            'data' => $data['workSchedules'],
        ];

        $service->postCollection($result);

        return $this->json([
            'success' => true,
        ]);
    }
}
