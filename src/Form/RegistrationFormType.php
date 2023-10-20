<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Validator\Constraints\Regex;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('firstName',TextType::class)
        ->add('lastName',TextType::class)
        ->add('email',EmailType::class)
        ->add('linkedin',UrlType::class)
        ->add('github',UrlType::class)
        ->add('city',TextType::class)
        ->add('sessionSD',DateType::class, ['widget' => 'single_text'])
        ->add('sessionED',DateType::class, ['widget' => 'single_text'])
        ->add('plainPassword', RepeatedType::class, [
            'constraints' => [
                new Regex('/^(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9]).{14,}$/',
                "You need at least 14 characters, 
                including 1 uppercase letter, 
                1 lowercase letter, and 1 number.")
            ],
            'mapped' => false,
            'type' => PasswordType::class,
            'invalid_message' => 'The password fields must match.',
            'options' => ['attr' => ['class' => 'password-field']],
            'required' => true,
            'label'=>false,
            'first_options' => [
                'attr' => ['placeholder' => 'Password'],
            ],
            'second_options' => [
                'attr' => ['placeholder' => 'Repeat Password'],
            ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ])
            ;
        }
        
        public function configureOptions(OptionsResolver $resolver): void
        {
            $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
