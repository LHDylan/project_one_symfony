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
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email:',
                'required' => true
            ])
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'User' => 'ROLE_USER',
                    'Editor' => 'ROLE_EDITOR',
                    'Admin' => 'ROLE_ADMIN'
                ],
                'label' => 'Role:',
                'required' => false,
                'expanded' => true,
                'multiple' => true
            ])
            ->add('password', PasswordType::class, [
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Complete field'
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Password should be at least {{ limit }} characters',
                        'max' => 100
                    ])
                ]
            ])
            ->add('fName', TextType::class, [
                'label' => 'First name:',
                'required' => true
            ])
            ->add('lName', TextType::class, [
                'label' => 'Last name:',
                'required' => true
            ])
            ->add('address', TextType::class, [
                'label' => 'Your address:',
                'required' => true
            ])
            ->add('zipCode', IntegerType::class, [
                'label' => 'Zip Code:',
                'required' => true
            ])
            ->add('city', TextType::class, [
                'label' => 'City:',
                'required' => true
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
