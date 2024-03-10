<?php

namespace App\Form;

use App\Entity\Advert;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdvertType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'choices' => ['Vente' => Advert::TYPE_OFFER, 'Location' => Advert::TYPE_LOCATION],
                'label' => 'Type d\'annonce',
                'attr' => ['class' => 'mdb-select md-outline md-form dropdown-primary'],
                'placeholder' => 'Type d\'annonce',
                'required' => false
            ])
            ->add('title', TextType::class, [
                'label' => 'Titre de l\'annonce',
                'help' => 'Saisissez un titre décrivant précisément le bien.'
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description de l\'annonce',
                'attr'  => ['class' => 'form-control md-textarea', 'rows'  => 5],
                'help' => 'Décrivez précisément votre bien, en indiquant son état, ses caractéristiques, ainsi tout autre information importante pour l\'acquéreur.'
            ])
            ->add('price', IntegerType::class, [
                'label' => 'Prix (F CFA)',
                'attr' => ['min' => 0],
                'help' => 'Laisser ce champ vide si vous voulez que le prix soit sur demande',
                'required' => false
            ])
            ->add('city', TextType::class, ['label' => 'Ville'])
            ->add('district', TextType::class, [
                'label' => 'Quartier ou commune (facultatif)',
                'help' => 'Donnez des détails sur votre emplacement (quartier, rue, ...). Ex Riviera 2 marcher d\'anono.'
            ])
            ->add('enabled', SwitchType::class, [
                'label' => 'Activé',
                'attr' => ['class' => 'mdb-select md-outline md-form dropdown-primary'],
                'placeholder' => false,
                'expanded' => false
            ]);
    }

        public function configureOptions(OptionsResolver $resolver): void
        {
        $resolver->setDefaults([
            'data_class' => Advert::class,
        ]);
    }
}

