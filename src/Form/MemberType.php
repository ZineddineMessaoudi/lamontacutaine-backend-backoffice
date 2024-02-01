<?php

namespace App\Form;

use App\Entity\Member;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Validator\Constraints as Assert;

class MemberType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, [
                'label' => 'Prénom',
                'attr' => [
                    'maxlength' => 50
                ],
                'mapped' => false,
                'constraints' => [
                    new NotBlank(null, "Ce champs ne peut pas être vide")
                ]
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom',
                'attr' => [
                    'maxlength' => 50
                ],
                'mapped' => false,
                'constraints' => [
                    new NotBlank(null, "Ce champs ne peut pas être vide")
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse email',
                'attr' => [
                    'maxlength' => 100
                ],
                'mapped' => false,
                'constraints' => [
                    new Email(null, "Email invalide"),
                    new NotBlank(null, "Ce champs ne peut pas être vide")
                ]
            ])
            ->add('newsletter_subscriber', ChoiceType::class, [
                'label' => 'L\'utilisateur souhaite recevoir la newsletter ?',
                'choices' => [
                    'Oui' => true,
                    'Non' => false
                ],
                'multiple' => false,
                'mapped' => false
            ])
            ->add('street_name', TextType::class, [
                'label' => 'Nom de la rue',
            ])
            ->add('street_number', NumberType::class, [
                'label' => 'N° de rue',
            ])
            ->add('zip_code', TextType::class, [
                'label' => 'Code postal',
                'attr' => [
                    'maxlength' => 10
                ]
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
                'attr' => [
                    'maxlength' => 100
                ]
            ])
            ->add('country', TextType::class, [
                'label' => 'Pays',
                'attr' => [
                    'maxlength' => 50
                ]
            ])
            ->add('phone', TextType::class, [
                'label' => 'N° de téléphone',
                'attr' => [
                    'maxlength' => 20
                ]
            ])
            ->add('function', ChoiceType::class, [
                'label' => 'Fonction dans l\'association',
                'choices'  => [
                    'Membre' => 'Membre',           
                    'Secrétaire' => 'Secrétaire',
                    'Secrétaire adjoint' => 'Secrétaire adjoint',
                    'Trésorier' => 'Trésorier',
                    'Trésorier adjoint' => 'Trésorier adjoint',
                    'Vice-Président' => 'Vice-Président',
                    'Président' => 'Président',
                ],
                'multiple' => false
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'label' => false,
                'first_options' => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Confirmez votre mot de passe'],
                'invalid_message' => 'Les mots de passe ne sont pas identiques',
                'attr' => [
                    'maxlength' => 100
                ],
                'constraints' => $options['addNotBlankConstraint'] ? [new NotBlank(null, "Ce champs ne peut pas être vide")] : []
            ])
            ->add('membership_statut', ChoiceType::class, [
                'label' => 'Statut de l\'abonnement',
                'choices'  => [
                    'Abonné' => true,
                    'Non abonné' => false
                ],
                'multiple' => false
            ])
            ->add('isAdmin', ChoiceType::class, [
                'label' => 'Cet utilisateur est-il un administrateur ?',
                'choices'  => [
                    'Non' => 'Non',
                    'Oui' => 'Oui'
                ],
                'placeholder' => 'Veuillez sélectionner une option',
                "constraints" => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez sélectionner une option.',
                    ]),
                ],
                'multiple' => false,
                'mapped' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Member::class,
            'addNotBlankConstraint' => false,
        ]);
    }
}
