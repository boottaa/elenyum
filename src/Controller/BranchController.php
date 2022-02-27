<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Entity\Role;
use App\Exception\ArrayException;
use App\Service\BranchService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BranchController extends AbstractController
{
    #[Route('/api/branch/get', name: 'apiBranchGet')]
    public function list(Request $request): Response
    {
        $user = $this->getUser();
        if (!$user instanceof Employee) {
            return $this->json((new ArrayException('Пользователь не найден', 202))->toArray());
        }

        return $this->json([
            'success' => true,
            'item' => $user->getBranch(),
        ]);
    }

    /**
     * @param Request $request
     * @param BranchService $service
     * @return Response
     * @throws \JsonException
     */
    #[IsGranted('ROLE_'.Role::BRANCH_SETTING)]
    #[Route('/api/branch/put/{branchId<\d+>?}', name: 'apiBranchPut', methods: 'PUT')]
    public function put(Request $request, BranchService $service): Response
    {
        $user = $this->getUser();
        if (!$user instanceof Employee) {
            return $this->json((new ArrayException('Пользователь не найден', 202))->toArray());
        }

        $data['branch'] = $user->getBranch();
        $data['data'] = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $service->put($data);

        return $this->json([
            'success' => true,
            'item' => $user->getBranch(),
        ]);
    }
}
