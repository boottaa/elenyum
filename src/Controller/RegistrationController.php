<?php

namespace App\Controller;

use App\Entity\Branch;
use App\Entity\Company;
use App\Entity\Employee;
use App\Entity\EmployeeRole;
use App\Entity\Role;
use App\Exception\ArrayException;
use App\Repository\EmployeeRepository;
use App\Repository\RoleRepository;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    /**
     * @param Request $request
     * @param UserPasswordHasherInterface $userPasswordHasher
     * @param EntityManagerInterface $em
     * @param EmployeeRepository $employeeRepository
     * @param RoleRepository $roleRepository
     * @return Response
     * @throws \JsonException
     */
    #[Route('/api/register', name: 'api_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $em,
        EmployeeRepository $employeeRepository,
        RoleRepository $roleRepository
    ): Response {
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $em->beginTransaction();
        try {
            if ($employeeRepository->findOneBy(['email' => $data['email']]) !== null) {
                $e = new ArrayException('Такой пользователь уже зарегистрирован', '201');

                return $this->json($e->toArray());
            }

            $employee = new Employee();
            $employee->setStatus(0);
            $employee->setPhone($data['phone']);
            $employee->setName($data['user_name']);
            $employee->setEmail($data['email']);
            $em->persist($employee);

            $company = new Company();
            $company->setName($data['company_name']);
            $em->persist($company);
            $employee->setCompany($company);

            $branch = new Branch();
            $branch->setCompany($company);
            $branch->setName($data['company_name'].' - 1');
            $employee->setBranch($branch);
            $em->persist($branch);

            $employeeRole = new EmployeeRole();
            $employeeRole->setEmployee($employee);

            foreach ($roleRepository->findAll() as $role) {
                $employeeRole->addRole($role->getId());
                $em->persist($employeeRole);
            }

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

        $this->emailVerifier->sendEmailConfirmation(
            'app_verify_email',
            $employee,
            (new TemplatedEmail())
                ->from(new Address('noreplay@elenyum.com', 'Elenyum'))
                ->to($employee->getEmail())
                ->subject('Пожалуйста подтвердите ваш email')
                ->htmlTemplate('registration/confirmation_email.html.twig')
        );

        return $this->json([
            'success' => true,
            'message' => 'На указанный почтовый адрес отправлено письмо. Пожалуйста подтвердите ваш email',
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            return $this->json([
                'success' => false,
                'message' => '',
            ]);
        }

        return $this->json([
            'success' => true,
            'message' => 'Email confirmed',
        ]);
    }
}
