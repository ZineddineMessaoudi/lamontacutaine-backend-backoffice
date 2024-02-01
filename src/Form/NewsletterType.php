<?php

namespace App\Form;

use App\Model\Newsletter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class NewsletterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('subject', TextType::class, [
                'label' => 'Objet de la newsletter',
                'attr' => [
                    'maxlength' => 100
                ],
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champs ne peut pas être vide'
                    ])
                ]
            ])
            ->add('title', TextType::class, [
                'label' => 'Titre de la newsletter',
                'attr' => [
                    'maxlength' => 100
                ],
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champs ne peut pas être vide'
                    ])
                ]
            ])
            ->add('body', TextareaType::class, [
                'label' => 'Contenu de la newsletter',
                'attr' => [
                    'maxlength' => 1000,
                    'rows' => 10
                ],
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champs ne peut pas être vide'
                    ])
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Newsletter::class,
        ]);
    }
}
