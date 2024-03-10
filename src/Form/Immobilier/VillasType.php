<?php

namespace App\Form\Immobilier;

use App\Entity\Advert;
use App\Form\AdvertType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VillasType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('surface', IntegerType::class, ['label' => 'Surface (m²)'])
            ->add('nombrePiece', ImmobilierNombrePieceChoiceType::class, [
                'label' => 'Nombre de pièces',
                'attr' => ['class' => 'mdb-select md-outline md-form dropdown-primary'],
                'placeholder' => 'Nombre de pièces'
            ])
            ->add('nombreChambre', IntegerType::class, ['label' => 'Nombre de chambre(s)'])
            ->add('nombreSalleBain', IntegerType::class, ['label' => 'Nombre de salle(s) de bain'])
            ->add('dateConstruction', ImmobilierYearChoiceType::class, [
                'label' => 'Date de construction (facultatif)',
                'attr' => ['class' => 'mdb-select md-outline md-form dropdown-primary'],
                'placeholder' => 'Date de construction',
                'required' => false,
            ])
            ->add('standing', ImmobilierStandingChoiceType::class, [
                'label' => 'Standing (facultatif)',
                'attr' => ['class' => 'mdb-select md-outline md-form dropdown-primary'],
                'placeholder' => 'Standing (facultatif)',
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'validation_groups' => ['Default', 'VILLA']
        ]);
    }

    public function getParent(): string
    {
        return AdvertType::class;
    }
}
