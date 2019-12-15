<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid() ) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setRoles(['ROLE_USER']);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

//    /**
//     * @Route("/register/confirm", name="confirm_registration")
//     * @param $sendTo
//     * @return bool
//     */
//    public function confirmationMail($sendTo)
//    {
//        $transport = (new Swift_SmtpTransport('lazardugalic1@gmail.com', 25))
//            ->setUsername('Lazar Dugalic')
//            ->setPassword('L@zardugalic1');
//        $mailer = new Swift_Mailer($transport);
//
//        // Create a message
//        $message = (new Swift_Message('test'))
//            ->setFrom('lazardugalic1@gmail.com')
//            ->setTo($sendTo)
//            ->setBody($this->renderView('registration/confirmation_email.html.twig', [
//                'recipient' => $sendTo
//            ]), 'text/html');
//        $mailer->send($message);
//
//        return true;
//    }
}
