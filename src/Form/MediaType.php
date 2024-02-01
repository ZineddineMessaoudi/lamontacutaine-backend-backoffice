<?php

namespace App\Form;

use App\Entity\Event;
use App\Entity\Media;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints\NotBlank;

class MediaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('imageFile', FileType::class, [
                'multiple' => $options['multiple_mass'],
                'label' => 'Ajouter vos images',
                'help' => $options['help_mass'],
                'mapped' => false,
                'required' => false,
                'constraints' => $options['constraints_mass']
            ])

            ->add('event', EntityType::class, [
                'class' => Event::class,
                'choice_label' => 'title',
                'placeholder' => 'Sélectionnez un évènement',
                'mapped'    => false,
                'constraints' => $options['constraints_mass_event']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Media::class,
            'multiple_mass' => false,
            'constraints_mass' => [
                new NotBlank([
                    'message' => "Merci d'ajouter un fichier",
                ]),                
                new Image([
                    'maxSize' => '20M'
                ])
            ],
            'help_mass' => null,
            'constraints_mass_event' => [
            ],
        ]);
    }
}
