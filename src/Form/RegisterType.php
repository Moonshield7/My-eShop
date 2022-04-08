<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class,[
                'label' => 'Email :',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut être vide.',
                    ]),
                    new Length([
                        'max' => 180,
                        'maxMessage' => 'Votre email ne peut dépasser {{ limit }} caractères',
                    ]),
                    new Email([
                        'message' => 'Votre email n\'est pas valide. Exemple : xxx@xxx.xx',
                    ]),
                ]
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe :',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut être vide.',
                    ]),
                    new Length([
                        'max' => 255,
                        'min' => 4,
                        'maxMessage' => 'Votre mot de passe ne peut dépasser {{ limit }} caractères',
                        'minMessage' => 'Votre mot de passe doit comporter au minimum {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Prénom :',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut être vide.',
                    ]),
                    new Length([
                        'max' => 50,
                        'min' => 2,
                        'maxMessage' => 'Votre prénom ne peut dépasser {{ limit }} caractères',
                        'minMessage' => 'Votre prénom doit comporter au minimum {{ limit }} caractères',
                    ])
                ],
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom :',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut être vide.',
                    ]),
                    new Length([
                        'max' => 50,
                        'min' => 2,
                        'maxMessage' => 'Votre nom ne peut dépasser {{ limit }} caractères',
                        'minMessage' => 'Votre nom doit comporter au minimum {{ limit }} caractères',
                    ])
                ],
            ])
            ->add('gender', ChoiceType::class, [
                'label' => 'Civilité :',
                'expanded' => true,
                'choices' => [
                    'Homme' => 'h',
                    'Femme' => 'f',
                    'Autre' => 'x'
                ],
                'attr' => [
                    'class' => 'd-flex gap-3 justify-content-center',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut être vide.',
                    ]),
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label'=> 'Valider',
                // Cette option permet de désactiver le validator HTML (front)
                    # => form_start(form, {'attr': {'novalidate': novalidate}})
                'validate' => false,
                'attr' => [
                    'class' => 'mt-4 d-block col-12 btn btn-success',
                ]
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
