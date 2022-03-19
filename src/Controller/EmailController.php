<?php

namespace App\Controller;

use App\Service\EmailService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class EmailController extends AbstractController
{
    private const _NOREPLAY = 'noreply@elenyum.ru';

    public function __construct(
        private MailerInterface $mailer
    ) {
    }

    /**
     * https://elenyum.ru/api/email/test
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    #[Route('/api/email/test', name: 'aaaa')]
    public function index(): Response
    {

        try {
            $email = (new Email())
                ->from(self::_NOREPLAY)
                ->to('bootta@yandex.ru')
                //->cc('cc@example.com')
                //->bcc('bcc@example.com')
                //->replyTo('fabien@example.com')
                //->priority(Email::PRIORITY_HIGH)
                ->subject('Time for Symfony Mailer!')
                ->text('Sending emails is fun again!')
                ->html('<p>See Twig integration for better HTML integration!</p>');

            $this->mailer->send($email);
        } catch (\Exception $e) {
            dd($e->getMessage());
        }

        return $this->json([
            'success' => true,
        ]);
    }

    #[Route('/api/email/verify', name: 'apiEmailVerify')]
    public function verifyUserEmail(Request $request, EmailService $emailService): Response
    {
        dd($request);
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $emailService->handleEmailConfirmation($request, $this->getUser());
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
