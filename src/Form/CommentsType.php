<?php

namespace App\Form;

use App\Entity\Comments;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class CommentsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'title:',
                'required' => true,
                'constraints' => [
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Title must be at least {{limit}} characters.',
                        'max' => 100,
                        'maxMessage' => 'Title shoundn\'t exceed {{limit}} characters.',
                    ]),
                ],
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Content:',
                'required' => false,
            ])
            ->add('note', RangeType::class, [
                'attr' => [
                    'min' => 0,
                    'max' => 5,
                ],
                'help' => 'Did you appreciate this article ?',
                'required' => true,
            ])
            ->add('rgpd', CheckboxType::class, [
                'help' => 'Accept the confidentiality and privacy policies.',
                'constraints' => [
                    new NotBlank([
                        'message' => 'You must accept the privacy policies before posting your comment.',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comments::class,
        ]);
    }
}
