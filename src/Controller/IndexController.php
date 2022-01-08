<?php

namespace App\Controller;

use App\Entity\Role;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    #[Route('/', name: 'appIndex')]
    public function index(): Response
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('login');
        }

        return $this->render('index/index.html.twig', []);
    }

    #[Route('/calendar', name: 'appCalendar')]
    public function calendar(): Response
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('login');
        }

        return $this->render('index/calendar.html.twig', []);
    }

    #[Route('/employee/post', name: 'employeePost')]
    public function employeeAdd(): Response
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('login');
        }

        return $this->render('index/employeePost.html.twig', []);
    }

    #[Route('/employee/list', name: 'employeeList')]
    public function employeeList(): Response
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('login');
        }

        return $this->render('index/employeeList.html.twig', []);
    }

    #[Route('/position/post', name: 'positionAdd')]
    public function positionAdd(): Response
    {
        if (!$this->isGranted('ROLE_'.Role::EMPLOYEE_POST)) {
            return $this->redirectToRoute('login');
        }

        return $this->render('index/positionPost.html.twig', []);
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
