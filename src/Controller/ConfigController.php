<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Exception\ArrayException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ConfigController extends AbstractController
{
    #[Route('/api/config/get', name: 'apiConfigGet')]
    public function list(Request $request): Response
    {
        $user = $this->getUser();
        if (! $user instanceof Employee) {
            return $this->json((new ArrayException('Пользователь не найден', 202))->toArray());
        }

        return $this->json([
            'success' => true,
            'branch' => $user->getBranch()->jsonSerialize(),
            'roles' => $user->getRoles(),
        ]);
    }
}
