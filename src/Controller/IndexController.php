<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    #[Route('/', name: 'appIndex')]
    public function index(): Response
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('appLogin');
        }

        return $this->render('index/index.html.twig', []);
    }

    #[Route('/calendar', name: 'appCalendar')]
    public function calendar(): Response
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('appLogin');
        }

        return $this->render('index/calendar.html.twig', []);
    }

    #[Route('/employee/add', name: 'employeeAdd')]
    public function employeeAdd(): Response
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('appLogin');
        }

        return $this->render('index/calendar.html.twig', []);
    }

    #[Route('/registration', name: 'registration')]
    public function registration(): Response
    {
        return $this->render('index/registration.html.twig', []);
    }

    #[Route('/login', name: 'login')]
    public function login(): Response
    {
        return $this->render('index/login.html.twig', []);
    }

    #[Route('/forgotPassword', name: 'forgotPassword')]
    public function forgotPassword(): Response
    {
        return $this->render('index/forgotPassword.html.twig', []);
    }
}
