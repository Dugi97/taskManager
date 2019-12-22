<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;


class RegistrationService
{
    public $container;
    public $entityManager;
    public $twig;

    public function __construct(ContainerInterface $container, EntityManagerInterface $entityManager, Environment $twig)
    {
        $this->container = $container;
        $this->entityManager = $entityManager;
        $this->twig = $twig;
    }

    /**
     * @param $request
     * @param $verificationCode
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function sendVerificationMail($request, $verificationCode)
    {
        $fullName = $request->get('firstName')->getData().' '.$request->get('lastName')->getData();
        $html = $this->twig->render('registration/confirmation_email.html.twig', [
            'name' => $fullName,
            'code' => $verificationCode
        ]);
        MailService::sendMail('zola77kv@gmail.com', $request->get('email')->getData(), 'Email verification',$html, 'monamonamona');
    }

    /**
     * @param $code
     * @param $userId
     * @return bool
     */
    public function checkVerification($code, $userId)
    {
        /** @var User $user */
        $user = $this->entityManager->getRepository(User::class)->find($userId);
        if ($code == $user->getVerificationCode()) {
            $user->setVerificationCode('verified');
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return true;
        }

        return false;
    }
}