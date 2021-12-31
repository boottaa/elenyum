<?php

namespace App\Controller;

use App\Entity\Employee;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/api/login", name="api_login")
     */
    public function login(EntityManagerInterface $em): Response
    {
        if ($this->getUser() === null) {
            return $this->json([
                'success' => false,
                'error' => 'Авторизация не выполнена',
            ]);
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

    #[Route('/api/ping', name: 'api_ping')]
//    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[IsGranted('ROLE_ADMIN')]
    public function ping(
        AuthenticationUtils $authenticationUtils
    ): Response {
        return $this->json(['response' => 'pong']);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(): void
    {
        throw new \LogicException(
            'This method can be blank - it will be intercepted by the logout key on your firewall.'
        );
    }
}
