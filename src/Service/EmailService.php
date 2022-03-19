<?php

namespace App\Service;

use App\Entity\Employee;
use App\Repository\EmployeeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class EmailService extends BaseAbstractService
{
    private const _NOREPLAY = 'noreply@elenyum.ru';

    public function __construct(
        private EmployeeRepository $employeeRepository,
        private VerifyEmailHelperInterface $verifyEmailHelper,
        private MailerInterface $mailer,
        private EntityManagerInterface $em,
    ) {
    }

    /**
     * @param string $email
     * @return Employee|null
     */
    private function getUserByEmail(string $email): ?Employee
    {
        $employee = $this->employeeRepository->findOneBy(['email' => $email]);

        if ($employee instanceof Employee) {
            return $employee;
        }

        return null;
    }

    /**
     * @param Employee $employee
     * @return void
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function confirmationEmail(Employee $employee): void
    {
        $signatureComponents = $this->verifyEmailHelper->generateSignature(
            'apiEmailVerify',
            $employee->getId(),
            $employee->getEmail()
        );

        $template = (new TemplatedEmail())
            ->from(new Address(self::_NOREPLAY, 'Elenyum'))
            ->to($employee->getEmail())
            ->subject('Пожалуйста подтвердите ваш email')
            ->htmlTemplate('email/confirmation.html.twig');

        $context = $template->getContext();
        $context['signedUrl'] = $signatureComponents->getSignedUrl();
        $context['expiresAtMessageKey'] = $signatureComponents->getExpirationMessageKey();
        $context['expiresAtMessageData'] = $signatureComponents->getExpirationMessageData();

        $template->context($context);

        $this->mailer->send($template);
    }

    /**
     * @throws VerifyEmailExceptionInterface
     */
    public function handleEmailConfirmation(Request $request, Employee $employee): void
    {
        $this->verifyEmailHelper->validateEmailConfirmation($request->getUri(), $employee->getId(), $employee->getEmail());
        $employee->setStatus(Employee::STATUS_CONFIRMED);

        $this->em->persist($employee);
        $this->em->flush();
    }
}