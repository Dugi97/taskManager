<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
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

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     * @throws TransportExceptionInterface
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);


        if ($form->isSubmitted()) {
                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    ));
                $user->setRoles(['ROLE_USER']);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
//                $entityManager->flush();

                return $this->render('registration/verification_form.html.twig');
//            }
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }


//    /**
//     * @Route("/register/confirm", name="confirm_registration")
//     * @param $sendTo
//     * @param $fullName
//     * @param $randomCode
//     * @return bool
//     * @throws TransportExceptionInterface
//     */
//    public function sendMail($sendTo, $fullName, $randomCode)
//    {
//        $email = (new Email())
//            ->from('zola77kv@gmail.com')
//            ->to($sendTo)
//            ->priority(Email::PRIORITY_HIGH)
//            ->subject('Important Notification')
//            ->text('Lorem ipsum...')
//            ->html($this->renderView('registration/confirmation_email.html.twig', [
//                'name' => $fullName,
//                'code' => $randomCode
//            ]
//        ));
//
//        $transport = new GmailSmtpTransport('zola77kv@gmail.com', 'monamonamona');
//        $mailer = new Mailer($transport);
//        $mailer->send($email);
//
//        return true;
//    }

}
