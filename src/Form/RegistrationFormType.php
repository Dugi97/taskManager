<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Email',
                    'class' => 'reg-log-form-fields form-control',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter your email',
                ]),
    ]
            ])
            ->add('firstName', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'First name',
                    'class' => 'reg-log-form-fields form-control'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter your first name',
                    ]),
                ]
            ])
            ->add('lastName', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Last name',
                    'class' => 'reg-log-form-fields form-control'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter your last name',
                    ]),
                ]
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'label' => false,
                'attr' => [
                    'placeholder' => 'Password',
                    'class' => 'reg-log-form-fields form-control'
                ],
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('countryAndCity', CountryType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Country',
                    'class' => 'reg-log-form-fields form-control'
                ]
            ])
            ->add('dateOfBirth', BirthdayType::class, [
                'label' => 'Birthday',
                'placeholder' => ['year' => 'Year', 'month' => 'Month', 'day' => 'Day'],
                'attr' => [
                    'class' => 'reg-log-form-fields date-form-control'
                ]
            ])
//            ->add('profilePicture', FileType::class, [
//                'label' => false,
//                'attr' => [
//                    'placeholder' => 'Chose profile picture',
//                    'accept' => 'image/png, image/jpeg',
//                    'class' => ''
//                ]
//            ])
//            ->add('agreeTerms', CheckboxType::class, [
//                'mapped' => false,
//                'constraints' => [
//                    new IsTrue([
//                        'message' => 'You should agree to our terms.',
//                    ]),
//                ],
//                'attr' => ['class' => 'reg-log-form-fields']
//            ])
            ->add('Register', SubmitType::class,[
                'attr' => ['class' => 'btn btn-outline-dark btn-lg mb-2']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'attr' => ['novalidate' => true]
        ]);
    }
}
