<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Entity\Role;
use App\Exception\ArrayException;
use App\Service\EmployeeService;
use App\Validator\EmployeeValidator;
use DateTimeImmutable;
use DateTimeZone;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EmployeeController extends AbstractController
{
    /**
     * @param EmployeeService $service
     * @param Request $request
     * @return Response
     * @throws ArrayException
     */
    #[IsGranted(Role::ROLE_EMPLOYEE_EDIT)]
    #[Route('/api/employee/list', name: 'apiEmployeeList')]
    public function list(EmployeeService $service, Request $request): Response
    {
        $page = $request->get('page', 1);
        $user = $this->getUser();
        if (!$user instanceof Employee) {
            return $this->json((new ArrayException('Пользователь не найден', 202))->toArray());
        }
        $list = $service->list(['company' => $user->getCompany()], $page);

        return $this->json([
            'success' => true,
            'items' => $list->getResults(),
            'total' => $list->getNumResults(),
            'page' => $list->getCurrentPage(),
            'size' => $list->getPageSize(),
        ]);
    }

    /**
     * @param Request $request
     * @param EmployeeService $service
     * @return Response
     * @throws ArrayException
     */
    #[Route('/api/employee/listForCalendar', name: 'apiEmployeeListForCalendar')]
    public function listForCalendar(Request $request, EmployeeService $service): Response
    {
        $user = $this->getUser();
        if (!$user instanceof Employee) {
            return $this->json((new ArrayException('Пользователь не найден', 202))->toArray());
        }
        $params = ['company' => $user->getCompany()];
        if (!$this->isGranted(Role::ROLE_SHEDULE_ALL) && !$this->isGranted(Role::ROLE_SHEDULE_ME)) {
            return $this->json((new ArrayException('Нет прав', 202))->toArray());
        }
        //Если нет прав на просмотр всех задач то загружаем только текущего пользователя
        if ($this->isGranted(Role::ROLE_SHEDULE_ME) && !$this->isGranted(Role::ROLE_SHEDULE_ALL)) {
            $params['userId'] = $user->getId();
        }
        $start = $request->query->getInt('start');
        $params['start'] = DateTimeImmutable::createFromFormat('U', round($start / 1000))->setTimezone(
            new DateTimeZone('Europe/Moscow')
        );
        $end = $request->query->getInt('end');
        $params['end'] = DateTimeImmutable::createFromFormat('U', round($end / 1000))->setTimezone(
            new DateTimeZone('Europe/Moscow')
        );

        $list = $service->listForCalendar($params);

        return $this->json([
            'success' => true,
            'items' => $list->getResults(),
            'total' => $list->getNumResults(),
            'page' => $list->getCurrentPage(),
            'size' => $list->getPageSize(),
        ]);
    }

    #[IsGranted(Role::ROLE_EMPLOYEE_EDIT)]
    #[Route('/api/employee/get/{employeeId<\d+>}', name: 'apiEmployeeGet', methods: 'GET')]
    public function getEmployee(int $employeeId, EmployeeService $service): Response
    {
        $user = $this->getUser();
        if (!$user instanceof Employee) {
            return $this->json((new ArrayException('Пользователь не найден', 202))->toArray());
        }

        return $this->json([
            'success' => true,
            'item' => $service->get($employeeId),
        ]);
    }

    /**
     * @param int $employeeId
     * @param EmployeeService $service
     * @return Response
     */
    #[IsGranted(Role::ROLE_EMPLOYEE_EDIT)]
    #[Route('/api/employee/delete/{employeeId<\d+>}', name: 'apiEmployeeDelete', methods: 'DELETE')]
    public function delete(int $employeeId, EmployeeService $service): Response
    {
        $user = $this->getUser();
        if (!$user instanceof Employee) {
            return $this->json((new ArrayException('Пользователь не найден', 202))->toArray());
        }
        if ($user->getId() === $employeeId) {
            return $this->json((new ArrayException('Вы не можете удалить сами себя', 202))->toArray());
        }

        try {
            $service->del($employeeId);
        } catch (ArrayException $arrayException) {
            return $this->json($arrayException);
        }

        return $this->json([
            'success' => true,
        ]);
    }

    /**
     * Create resource
     *
     * @param Request $request
     * @param EmployeeService $service
     * @param EmployeeValidator $validator
     * @return Response
     * @throws ArrayException
     * @throws \JsonException
     */
    #[IsGranted(Role::ROLE_EMPLOYEE_EDIT)]
    #[Route('/api/employee/post', name: 'apiEmployeePost', methods: 'POST')]
    public function post(Request $request, EmployeeService $service, EmployeeValidator $validator): Response
    {
        $content = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        if ($validator->isValid($content)) {
            $user = $this->getUser();
            if (!$user instanceof Employee) {
                return $this->json((new ArrayException('Пользователь не найден', 202))->toArray());
            }
            $data['employee'] = $content;
            $data['user'] = $user;

            $employee = $service->post($data);

            return $this->json([
                'success' => true,
                'item' => $employee
            ]);
        }

        return $this->json([
            'success' => false,
            'message' => 'Не корректные данные',
            'error' => $validator->getErrors()
        ]);
    }

    /**
     * Update resource
     *
     * @param Request $request
     * @param EmployeeService $service
     * @param EmployeeValidator $validator
     * @return Response
     * @throws ArrayException
     * @throws \JsonException
     */
    #[IsGranted(Role::ROLE_EMPLOYEE_EDIT)]
    #[Route('/api/employee/put/{employeeId<\d+>?}', name: 'apiEmployeePut', methods: 'PUT')]
    public function put(Request $request, EmployeeService $service, EmployeeValidator $validator): Response
    {
        $content = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        if ($validator->isValid($content)) {
            $user = $this->getUser();
            if (!$user instanceof Employee) {
                return $this->json((new ArrayException('Пользователь не найден', 202))->toArray());
            }
            $data['employee'] = $content;

            $employee = $service->put($data);

            return $this->json([
                'success' => true,
                'item' => $employee,
            ]);
        }

        return $this->json([
            'success' => false,
            'message' => 'Не корректные данные',
            'errors' => $validator->getErrors()
        ]);
    }
}
