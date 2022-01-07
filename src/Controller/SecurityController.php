<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Entity\Role;
use App\Exception\ArrayException;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/api/login', name: 'api_login', methods: 'POST')]
    public function login(EntityManagerInterface $em): Response
    {
        if ($this->getUser() === null) {
            return $this->json((new ArrayException('Авторизация не выполнена'))->toArray());
        }

        $employee = $this->getUser();
        if ($employee instanceof Employee) {
            if ($employee->getStatus() === Employee::STATUS_NEW) {
                $this->json([
                    'success' => false,
                    'message' => 'Подтвердите email'
                ]);
            }

            $token = $employee->getUserIdentifier();
            $employee->setApiToken($token);
            $em->flush();

            return $this->json([
                'success' => true,
                'name' => $employee->getName(),
                'status' => $employee->getStatus(),
                'roles' => $employee->getRoles(),
                'token' => $employee->getApiToken(),
            ]);
        }

        return $this->json([
            'error' => 'Авторизация не выполнена',
        ]);
    }

    #[Route('/api/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException(
            'This method can be blank - it will be intercepted by the logout key on your firewall.'
        );
    }
}
