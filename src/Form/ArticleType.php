<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Tag;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Title:',
                'required' => true
            ])
            ->add('tags', EntityType::class, [
                'class' => Tag::class,
                'label' => 'Tags:',
                'required' => false,
                'expanded' => true,
                'multiple' => true,
                'choice_label' => 'title',
                'by_reference' => false
            ])
            ->add('imageFile', VichImageType::class, [
                'label' => 'Image:',
                'required' => false,
                'download_uri' => false,
                'image_uri' => true
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Content:',
                'required' => true
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
