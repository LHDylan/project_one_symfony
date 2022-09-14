<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Tag;
use Doctrine\ORM\EntityRepository;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Title:',
                'required' => true,
            ])
            ->add('tags', EntityType::class, [
                'class' => Tag::class,
                'query_builder' => function (EntityRepository $entityRepo) {
                    return $entityRepo->createQueryBuilder('t')
                        ->andWhere('t.active = true');
                },
                'label' => 'Tags:',
                'required' => false,
                'expanded' => true,
                'multiple' => true,
                'choice_label' => 'title',
                'by_reference' => false,
            ])
            ->add('articleImages', CollectionType::class, [
                'entry_type' => ArticleImageType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'delete_empty' => true,
                'prototype' => true,
                'by_reference' => false,
            ])
            ->add('content', CKEditorType::class, [
                'label' => 'Content:',
                'required' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
