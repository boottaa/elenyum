<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Exception\ArrayException;
use App\Service\SecurityService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{
    #[Route('/api/login', name: 'api_login', methods: 'POST')]
    public function login(SessionInterface $session): Response
    {
        if ($this->getUser() === null) {
            return $this->json(new ArrayException('Авторизация не выполнена'));
        }

        $employee = $this->getUser();
        if ($employee instanceof Employee) {
            if ($employee->getStatus() === Employee::STATUS_NEW) {
                $this->json([
                    'success' => false,
                    'message' => 'Подтвердите email',
                ]);
            }

            $session->start();

            return $this->json([
                'success' => true,
                'name' => $employee->getName(),
                'status' => $employee->getStatus(),
                'roles' => $employee->getRoles(),
                'token' => $session->getId(),
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

    /**
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     * @throws ArrayException
     * @throws \JsonException
     */
    #[Route('/api/forgotPassword', name: 'apiForgotPassword', methods: 'POST')]
    public function forgotPassword(Request $request, SecurityService $securityService): Response
    {
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $employee = $securityService->forgotPassword($data['email']);

        return $this->json([
            'success' => true,
            'message' => 'Инструкция для восстановления пароля отправлена на Email: ' . $employee->getEmail(),
        ]);
    }
}
