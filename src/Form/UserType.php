<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Vich\UploaderBundle\Form\Type\VichImageType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email:',
                'required' => true,
            ])
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'User' => 'ROLE_USER',
                    'Editor' => 'ROLE_EDITOR',
                    'Admin' => 'ROLE_ADMIN',
                ],
                'label' => 'Role:',
                'required' => false,
                'expanded' => true,
                'multiple' => true,
            ])
            ->add('password', PasswordType::class, [
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new Regex(
                        '/^((?=\S?[A-Z])(?=\S?[a-z])(?=\S*?[0-9]).{6,})\S$/',
                        'Votre mot de passe doit comporter au moins 6 caractères, une lettre majuscule, une lettre miniscule et 1 chiffre sans espace blanc'
                    ),
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                ],
            ])
            ->add('fName', TextType::class, [
                'label' => 'First name:',
                'required' => true,
            ])
            ->add('lName', TextType::class, [
                'label' => 'Last name:',
                'required' => true,
            ])
            ->add('address', TextType::class, [
                'label' => 'Your address:',
                'required' => true,
            ])
            ->add('zipCode', IntegerType::class, [
                'label' => 'Zip Code:',
                'required' => true,
            ])
            ->add('city', TextType::class, [
                'label' => 'City:',
                'required' => true,
            ])
            ->add('imageFile', VichImageType::class, [
                'label' => 'Image:',
                'required' => false,
                'download_uri' => false,
                'image_uri' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
