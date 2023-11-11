<?php

namespace App\Form;

use App\Entity\Post;
use Attribute;
use DateTime;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('text', TextType::class, [
                'attr' => [
                    'class' => 'editText', // Ajoutez la classe CSS pour le champ texte
                    'placeholder' => "What's happening?",
                ]
            ])
            ->add('pics', CollectionType::class, [
                'entry_type' => PostPicsType::class, // Créez un nouveau formulaire de type PostPicsType pour chaque image
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'publish',
                'attr' => [
                    'class' => 'publish-button',
                ],
            ]);
            
    }
      public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
