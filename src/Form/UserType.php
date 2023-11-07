<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email',EmailType::class)
            ->add('firstName',TextType::class)
            ->add('lastName',TextType::class)
                    
            ->add('city',TextType::class)
            ->add('sessionSD',DateType::class, ['widget' => 'single_text','html5' => false])
            ->add('sessionED',DateType::class, ['widget' => 'single_text','html5' => false])
            // ->add('image',FileType::class)
            ->add('github' ,UrlType::class)
            ->add('linkedin', UrlType::class)
            ->add('password', PasswordType::class, [
                'mapped' => false, // Ce champ ne sera pas mappé à l'entité utilisateur
                'label' => 'Current Password',
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
