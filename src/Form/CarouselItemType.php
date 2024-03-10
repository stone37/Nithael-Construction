<?php

namespace App\Form;

use App\Data\CarouselItemCrudData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CarouselItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, ['label' => 'Titre (facultatif)'])
            ->add('description', TextareaType::class, ['label' => 'Description (facultatif)'])
            ->add('url', TextType::class, ['label' => 'Url (facultatif)'])
            ->add('enabled', SwitchType::class, [
                'label' => 'Activée',
                'attr' => ['class' => 'mdb-select md-outline md-form dropdown-primary'],
                'placeholder' => false,
                'expanded' => false
            ])
            ->add('file', FileType::class, ['required' => false])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CarouselItemCrudData::class,
        ]);
    }
}
