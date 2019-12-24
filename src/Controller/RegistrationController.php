<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Service\RegistrationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Bridge\Google\Transport\GmailSmtpTransport;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param RegistrationService $registrationService
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, RegistrationService $registrationService): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {
            $verificationCode = substr(md5(mt_rand()), 0, 6);
            $registrationService->sendVerificationMail($form, $verificationCode);
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                ));
            $user->setRoles(['ROLE_USER']);
            $user->setVerificationCode($verificationCode);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->getVerificationForm($user->getId());
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/check/verification", name="check_verification")
     * @param Request $request
     * @param RegistrationService $registrationService
     * @return RedirectResponse|Response
     */
    public function checkVerification(Request $request, RegistrationService $registrationService)
    {
        if ($registrationService->checkVerification($request->get('verify_code_input'), $request->get('userId')) === false) {
           return $this->getVerificationForm($request->get('userId'));
        }

        return $this->redirectToRoute('app_login');
    }

    /**
     * @Route("/verification", name="verification")
     * @param $userId
     * @return Response
     */
    public function getVerificationForm($userId)
    {
        return $this->render('registration/verification_form.html.twig', [
            'userId' => $userId
        ]);
    }
}
