<?php

namespace App\Controller;

use App\Entity\Branch;
use App\Entity\Company;
use App\Entity\Employee;
use App\Entity\Location;
use App\Entity\Position;
use App\Entity\PositionRole;
use App\Entity\Signature;
use App\Exception\ArrayException;
use App\Repository\EmployeeRepository;
use App\Repository\RoleRepository;
use App\Service\EmailService;
use App\Service\SecurityService;
use App\Validator\RegistrationValidator;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use SymfonyCasts\Bundle\VerifyEmail\Exception\ExpiredSignatureException;

class RegistrationController extends AbstractController
{
    /**
     * @param Request $request
     * @param UserPasswordHasherInterface $userPasswordHasher
     * @param EntityManagerInterface $em
     * @param EmployeeRepository $employeeRepository
     * @param RoleRepository $roleRepository
     * @param RegistrationValidator $validator
     * @param \App\Service\EmailService $emailService
     * @return Response
     * @throws \JsonException
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $em,
        EmployeeRepository $employeeRepository,
        RoleRepository $roleRepository,
        RegistrationValidator $validator,
        EmailService $emailService,
    ): Response {
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        if ($validator->isValid($data)) {
            $em->beginTransaction();
            try {
                if ($employeeRepository->findOneBy(['email' => $data['email']]) !== null) {
                    $e = new ArrayException('Такой пользователь уже зарегистрирован', '201');

                    return $this->json($e->toArray());
                }

                $employee = new Employee();
                $employee->setStatus(Employee::STATUS_NEW);
                $employee->setPhone($data['phone']);
                $employee->setName($data['userName']);
                $employee->setEmail($data['email']);
                $em->persist($employee);

                $company = new Company();
                $company->setName($data['companyName']);
                $em->persist($company);

                $employee->setCompany($company);

                $branch = new Branch();
                $branch->setCompany($company);
                $branch->setName($data['companyName']);
                $employee->setBranch($branch);
                $location = new Location();
                $location->setAddress($data['address']);
                $em->persist($location);
                $branch->setLocation($location);
                $em->persist($branch);

                $positionRole = new PositionRole();
                foreach ($roleRepository->findAll() as $role) {
                    $positionRole->addRole($role->getId());
                }
                $em->persist($positionRole);

                $position = new Position();
                $position->setTitle($data['position']);
                $position->setPositionRole($positionRole);
                $position->addEmployee($employee);
                $position->setCompany($company);
                $position->setInCalendar(false);
                $em->persist($position);

                $employee->setPassword(
                    $userPasswordHasher->hashPassword(
                        $employee,
                        $data['password']
                    )
                );

                $em->flush();
                $em->commit();
            } catch (Exception $e) {
                $em->rollback();
                throw $e;
            }

            $emailService->confirmationEmail($employee);

            return $this->json([
                'success' => true,
                'message' => 'На указанный почтовый адрес отправлено письмо. Пожалуйста подтвердите ваш email',
            ]);
        }
        return $this->json([
            'success' => false,
            'message' => 'Не корректные данные',
        ]);
    }

    /**
     * @param Request $request
     * @param SecurityService $securityService
     * @return Response
     */
    #[Route('/api/recoveryPassword', name: 'apiRecoveryPassword', methods: 'POST')]
    public function recoveryPassword(Request $request, SecurityService $securityService): Response
    {
        try {
            $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
            $token = $request->get('token');
            $sign = $request->get('signature');

            $securityService->recoveryPassword($data['password'], $token, $sign);
        } catch (ExpiredSignatureException $e) {
            return $this->json(new ArrayException('Ссылка не корректна, попробуйте ещё раз', $e->getCode()));
        } catch (Exception $e) {
            return $this->json(new ArrayException($e->getMessage(), $e->getCode()));
        }

        return $this->json([
            'success' => true,
            'message' => 'Ваш пароль успешно изменён'
        ]);
    }
}
