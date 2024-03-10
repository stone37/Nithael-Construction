<?php

namespace App\Form\Filter;

use App\Entity\Advert;
use App\Form\AdvertCategoryChoiceType;
use App\Model\AdvertSearch;
use App\Repository\AdvertRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdvertFilterType extends AbstractType
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
            /*->add('price', PriceFilterType::class, ['required' => false, 'label' => false])*/;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AdvertSearch::class,
            'csrf_protection' => false,
            'method' => 'GET'
        ]);
    }

    public function getBlockPrefix(): ?string
    {
        return '';
    }
}