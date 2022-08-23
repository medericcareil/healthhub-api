<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Gregwar\CaptchaBundle\Type\CaptchaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pseudo', TextType::class, [
                'label' => 'Pseudo *',
                'constraints' => [
                    new NotBlank(['message' => 'Vous devez renseigner un pseudo']),
                    new Length([
                        'min' => 2,
                        'max' => 20,
                        'minMessage' => 'Le pseudo doit faire {{ limit }} caractères minimum',
                        'maxMessage' => 'Le pseudo doit faire {{ limit }} caractères maximum',
                    ])
                ],
            ])
            ->add('gender', ChoiceType::class, [
                'label' => 'Genre biologique *',
                'choices' => [
                    'Femme' => true,
                    'Homme' => false
                ],
                'multiple' => false,
                'expanded' => true,
                'constraints' => [
                    new NotNull(['message' => 'Vous devez renseigner un genre biologique'])
                ]
            ])
            ->add('birthdate', DateType::class, [
                'label' => 'Date de naissance *',
                'input' => 'datetime_immutable',
                'model_timezone' => 'Europe/Paris',
                'widget' => 'single_text',
                'constraints' => [
                    new NotNull([
                        'message' => 'Vous devez renseigner votre de date de naissance'
                    ]),
                ],
                'invalid_message' => 'Format de date invalide'
            ])
            ->add('image', FileType::class, [
                'label' => 'Photo de profil',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '3000k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/svg+xml',
                            'image/png',
                        ],
                        'maxSizeMessage' => 'L\'image ne doit pas dépasser 3mo',
                        'mimeTypesMessage' => 'Veuillez téléverser un fichier aux formats "jpeg, jpg, png, svg"',
                    ])
                ],
            ])
            ->add('email', RepeatedType::class, [
                'type' => EmailType::class,
                'invalid_message' => 'Les emails doivent correspondre',
                'first_options'   => ['label' => 'Email *'],
                'second_options'  => ['label' => 'Confirmation d\'email *'],
                'constraints' => [
                    new NotBlank(['message' => 'Vous devez renseigner un email']),
                    new Email(['message' => 'Email non valide']),
                    new Length([
                        'min' => 4,
                        'max' => 120,
                        'minMessage' => 'L\'email doit faire {{ limit }} caractères minimum',
                        'maxMessage' => 'L\'email doit faire {{ limit }} caractères maximum',
                    ])
                ],
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe doivent correspondre',
                'first_options'   => ['label' => 'Mot de passe *'],
                'second_options'  => ['label' => 'Confirmation de mot de passe *'],
                'constraints'     => [
                    new NotBlank([
                        'message' => 'Vous devez renseigner un mot de passe',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit faire {{ limit }} caractères minimum',
                        'max' => 4096,
                    ]),
                    new Regex([
                        'pattern' => "#(?=.*[A-Z])#",
                        'message' => 'Le mot de passe doit contenir au moins une majuscule'
                    ]),
                ],
            ])
            ->add('captcha', CaptchaType::class, [
                'label' => 'Je ne suis pas un robot *',
                'invalid_message' => 'Mauvaise valeur pour le code visuel',
                'attr' => ['placeholder' => 'Recopier le texte ci-dessus *'],
                'width' => 200,
                'height' => 80,
                'length' => 4,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void {}
}
