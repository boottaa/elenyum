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

        return $this->render('index/calendar.html.twig', []);
    }

    #[Route('/calendar', name: 'appCalendar')]
    public function calendar(): Response
    {
        if (! $this->isGranted(Role::ROLE_SHEDULE_ALL) && ! $this->isGranted(Role::ROLE_SHEDULE_ME)) {
            return $this->redirectToRoute('appIndex');
        }

        return $this->render('index/calendar.html.twig', []);
    }

    #[Route('/employee/post/{id<\d+>?}', name: 'employeePost')]
    public function employeePost(): Response
    {
        if (! $this->isGranted(Role::ROLE_EMPLOYEE_EDIT)) {
            return $this->redirectToRoute('appIndex');
        }

        return $this->render('index/employeePost.html.twig');
    }

    #[Route('/employee/list', name: 'employeeList')]
    public function employeeList(): Response
    {
        if (! $this->isGranted(Role::ROLE_EMPLOYEE_EDIT)) {
            return $this->redirectToRoute('appIndex');
        }

        return $this->render('index/employeeList.html.twig', []);
    }

    #[Route('/position/post/{id<\d+>?}', name: 'positionPost')]
    public function positionPost(): Response
    {
        if (!$this->isGranted(Role::ROLE_POSITION_EDIT)) {
            return $this->redirectToRoute('appIndex');
        }

        return $this->render('index/positionPost.html.twig', []);
    }

    #[Route('/position/list', name: 'positionList')]
    public function positionList(): Response
    {
        if (!$this->isGranted(Role::ROLE_POSITION_EDIT)) {
            return $this->redirectToRoute('appIndex');
        }

        return $this->render('index/positionList.html.twig', []);
    }

    #[Route('/operation/post/{id<\d+>?}', name: 'operationPost')]
    public function operationPost(): Response
    {
        if (!$this->isGranted(Role::ROLE_OPERATION_EDIT)) {
            return $this->redirectToRoute('appIndex');
        }

        return $this->render('index/operationPost.html.twig', []);
    }

    #[Route('/operation/list', name: 'operationList')]
    public function operationList(): Response
    {
        if (!$this->isGranted(Role::ROLE_OPERATION_EDIT)) {
            return $this->redirectToRoute('appIndex');
        }

        return $this->render('index/operationList.html.twig', []);
    }

    #[Route('/branch/setting', name: 'branchSetting')]
    public function branchSetting(): Response
    {
        if (!$this->isGranted(Role::ROLE_BRANCH_SETTING)) {
            return $this->redirectToRoute('appIndex');
        }

        return $this->render('index/branchSetting.html.twig', []);
    }

    #[Route('/workSchedule/post/{id<\d+>?}', name: 'workSchedulePost')]
    public function workSchedulePost(): Response
    {
        if (! $this->isGranted(Role::ROLE_WORK_SCHEDULE_EDIT) && ! $this->isGranted(Role::ROLE_WORK_SCHEDULE_VIEW)) {
            return $this->redirectToRoute('login');
        }

        return $this->render('index/workSchedulePost.html.twig', []);
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

    #[Route('/recoveryPassword', name: 'recoveryPassword')]
    public function recoveryPassword(): Response
    {
        return $this->render('index/recoveryPassword.html.twig', []);
    }

    #[Route('/client/post/{id<\d+>?}', name: 'clientPost')]
    public function clientPost(): Response
    {
        if (! $this->isGranted(Role::ROLE_EMPLOYEE_EDIT)) {
            return $this->redirectToRoute('appIndex');
        }

        return $this->render('index/clientPost.html.twig');
    }

    #[Route('/client/list', name: 'clientList')]
    public function clientList(): Response
    {
        if (! $this->isGranted(Role::ROLE_EMPLOYEE_EDIT)) {
            return $this->redirectToRoute('appIndex');
        }

        return $this->render('index/clientList.html.twig', []);
    }
}
