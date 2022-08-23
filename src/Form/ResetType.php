<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Validator\Constraints\Length;

class ResetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Votre email *',
                'constraints' => [
                    new NotBlank(['message' => 'Vous devez renseigner un email']),
                    new Email(['message' => 'Email non valide']),
                    new Length([
                        'min' => 4,
                        'max' => 120,
                        'minMessage' => 'L\'email doit faire {{ limit }} caractères minimum',
                        'maxMessage' => 'L\'email doit faire {{ limit }} caractères maximum',
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
