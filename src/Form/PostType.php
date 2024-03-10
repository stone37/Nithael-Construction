<?php

namespace App\Form;

use App\Data\PostCrudData;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, ['label' => 'Titre'])
            ->add('content', CKEditorType::class, [
                'label' => 'Contenu',
                'config' => ['height' => '200']
            ])
            ->add('online', ChoiceType::class, [
                'choices' => ['Oui' => true, 'Non' => false],
                'label' => 'Publier maintenant',
                'attr' => ['class' => 'mdb-select md-outline md-form dropdown-primary']
            ])
            ->add('category', CategoryChoiceType::class, [
                'attr' => ['class' => 'mdb-select md-outline md-form dropdown-primary'],
                'label' => 'Catégorie (facultatif)',
                'placeholder' => 'Catégorie (facultatif)'
            ])
            ->add('file', FileType::class, ['required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PostCrudData::class,
        ]);
    }
}
