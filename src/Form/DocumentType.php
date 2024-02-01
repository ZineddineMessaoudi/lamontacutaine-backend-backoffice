<?php

namespace App\Form;

use App\Entity\Document;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\NotBlank;



class DocumentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('documentFile', FileType::class, [
                
                'multiple' => false,
                'label' => 'Document',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => "Merci d'ajouter un fichier",
                    ]), 
                    new File([
                        'maxSize' => '20M',
                        'mimeTypes' => [
                            'application/pdf',
                            'application/x-pdf',
                        ]
                    ])
                ],
            ])
            ->add('title', TextType::class, [
                'label'  => 'Nom du document',
            ])
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'label'  => 'Date du document',
                'input' => 'datetime',
                'data_class' => Datetime::class
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Type de document',
                'choices' => [
                    'Rapport Moral' => 'rapport_moral',
                    'Rapport d\'Activité' => 'rapport_activite',
                    'Rapport Financier' => 'rapport_financier',
                    'Privé (reservé aux adhérents)' => 'document_adherent'
                ],
                'multiple' => false,
                'mapped' => false
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Document::class,
        ]);
    }
}
