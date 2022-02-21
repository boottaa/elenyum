<?php

namespace App\Controller;

use App\Entity\Branch;
use App\Entity\Company;
use App\Entity\Employee;
use App\Entity\Location;
use App\Entity\Position;
use App\Entity\PositionRole;
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
     * @throws Exception
     */
    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
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

        $this->emailVerifier->sendEmailConfirmation(
            'api_verify_email',
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

    #[Route('/api/verify/email', name: 'api_verify_email')]
    public function verifyUserEmail(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            return $this->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ]);
        }

        return $this->json([
            'success' => true,
            'message' => 'Email confirmed',
        ]);
    }
}
