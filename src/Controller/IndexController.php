<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[Route('/', name: 'appIndex')]
    public function index(): Response
    {
        return $this->render('index/index.html.twig', [
            'controller_name' => 'IndexController',
        ]);
    }

    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[Route('/calendar', name: 'appCalendar')]
    public function calendar(): Response
    {
        return $this->render('index/calendar.html.twig', [
            'controller_name' => 'IndexController',
        ]);
    }

    #[Route('/registration', name: 'appRegistration')]
    public function registration(): Response
    {
        return $this->render('index/registration.html.twig', []);
    }

    #[Route('/login', name: 'appLogin')]
    public function login(): Response
    {
        return $this->render('index/login.html.twig', []);
    }

    #[Route('/forgotPassword', name: 'appForgotPassword')]
    public function forgotPassword(): Response
    {
        return $this->render('index/forgotPassword.html.twig', []);
    }
}
