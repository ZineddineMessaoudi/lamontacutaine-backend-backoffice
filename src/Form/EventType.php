<?php

namespace App\Form;

use App\Entity\Event;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label'  => 'Nom de l\'évènement'
            ])
            ->add('description', TextareaType::class, [
                'label'  => 'Description'
            ])
            ->add('info_highlight', TextareaType::class, [
                'label'  => 'Informations pratiques'
            ])
            ->add('start_date', DateType::class, [
                'widget' => 'single_text',
                'label'  => 'Début de l\'évènement'
            ])
            ->add('end_date', DateType::class, [
                'widget' => 'single_text',
                'label'  => 'Fin de l\'évènement'
            ])
            ->add('inscription_end_date', DateType::class, [
                'widget' => 'single_text',
                'label'  => 'Fin des inscriptions'
            ])
            ->add('maximum_capacity', IntegerType::class, [
                'label'  => 'Nombre de places'
            ])
            ->add('event_location', TextType::class, [
                'label'  => 'Lieu de l\'évènement'
            ])
            ->add('price', IntegerType::class, [
                'label'  => 'Prix de l\'inscription'
            ])
            ->add('hello_asso_url', TextType::class, [
                'label'  => 'URL de Helloasso'
            ])
            ->add('open_to_trader', CheckboxType::class, [
                'label'  => 'Evènement ouvert aux commmerçants'
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'multiple' => true,
                'label'  => 'Catégorie(s)',
                'expanded' => true,
                'constraints' => 
                    new Assert\Count([
                        'min' => 1,
                        'minMessage' => 'Veuillez sélectionner au moins une catégorie.',
                    ]),
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
