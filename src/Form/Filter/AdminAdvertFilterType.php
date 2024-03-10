<?php

namespace App\Form\Filter;

use App\Entity\Advert;
use App\Form\AdvertCategoryChoiceType;
use App\Model\Admin\AdvertSearch;
use App\Repository\AdvertRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminAdvertFilterType extends AbstractType
{
    public function __construct(private readonly AdvertRepository $repository)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('category', AdvertCategoryChoiceType::class, [
                'label' => 'Catégories',
                'attr' => ['class' => 'mdb-select md-outline md-form dropdown-primary'],
                'placeholder' => 'Catégories',
                'required' => false
            ])
            ->add('type', ChoiceType::class, [
                'choices' => ['Vente' => Advert::TYPE_OFFER, 'Location' => Advert::TYPE_LOCATION],
                'label' => 'Type',
                'attr' => ['class' => 'mdb-select md-outline md-form dropdown-primary'],
                'placeholder' => 'Type',
                'required' => false
            ])
            ->add('city', ChoiceType::class, [
                'choices' => $this->repository->getCities(),
                'label' => 'Ville',
                'attr' => ['class' => 'mdb-select md-outline md-form dropdown-primary'],
                'placeholder' => 'Ville',
                'required' => false
            ])
            ->add('enabled', ChoiceType::class, [
                'choices' => ['Oui' => true, 'Non' => false],
                'attr' => ['class' => 'mdb-select md-outline md-form dropdown-primary'],
                'label' => 'Active',
                'placeholder' => 'Active',
                'required' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AdvertSearch::class,
            'method' => 'GET',
            'csrf_protection' => false
        ]);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }
}